<?php
require_once __DIR__ . '/../src/init.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$views = isset($_COOKIE['feed_views']) ? (int)$_COOKIE['feed_views'] : 0;
$views++;
setcookie('feed_views', $views, time() + 3600 * 24 * 365, '/');

$show_profile_prompt = $_SESSION['show_profile_prompt'] ?? false;
unset($_SESSION['show_profile_prompt']);

$page_title = __('feed_title');
$active_page = 'feed';
require __DIR__ . '/../src/templates/header.php';
?>
<main class="main-container">
    <h1 id="feed_header"><?= __('feed_header') ?></h1>

    <?php if ($show_profile_prompt): ?>
        <div class="message info">Регистрация завершена! Предлагаем вам <a href="profile.php">заполнить данные своего профиля</a>.</div>
    <?php endif; ?>

    <div class="feed-controls">
        <label for="sort-order" id="sort_by"><?= __('sort_by') ?>:</label>
        <select id="sort-order">
            <option value="newest" id="newest"><?= __('newest') ?></option>
            <option value="oldest" id="oldest"><?= __('oldest') ?></option>
        </select>
    </div>

    <div id="recently-viewed-container">
        <h2 id="recently_viewed"><?= __('recently_viewed') ?></h2>
        <div id="recently-viewed-posts" class="feed-content"></div>
    </div>

    <div id="feed-container" class="feed-content"></div>
    <div id="loading" style="display: none; text-align: center;" id="loading-text"><?= __('loading') ?></div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', async function () {
        console.log('DOM fully loaded and parsed');
        let offset = 0;
        let limit = 5;
        let loading = false;
        const feedContainer = document.getElementById('feed-container');
        const recentlyViewedContainer = document.getElementById('recently-viewed-posts');
        const loadingIndicator = document.getElementById('loading');
        const sortOrderSelect = document.getElementById('sort-order');

        let i18n;
        try {
            const response = await fetch('/api/i18n.php');
            i18n = await response.json();
        } catch (error) {
            console.error('Error fetching translations:', error);
            i18n = {};
        }

        function t(key) {
            return i18n[key] || key;
        }

        // Set initial text content
        document.getElementById('feed_header').textContent = t('feed_header');
        document.getElementById('sort_by').textContent = t('sort_by');
        document.getElementById('newest').textContent = t('newest');
        document.getElementById('oldest').textContent = t('oldest');
        document.getElementById('recently_viewed').textContent = t('recently_viewed');
        document.getElementById('loading-text').textContent = t('loading');


        function fetchPosts() {
            loading = true;
            loadingIndicator.style.display = 'block';

            const sortOrder = sortOrderSelect.value;
            try {
                fetch(`/api/posts.php?offset=${offset}&limit=${limit}&sort=${sortOrder}`)
                    .then(response => response.json())
                    .then(posts => {
                        console.log(posts);
                        posts.forEach(post => {
                            const postElement = document.createElement('article');
                            postElement.className = 'post';
                            postElement.id = `post-${post.id}`;
                            postElement.innerHTML = `
                                <h2>${post.title}</h2>
                                <p>${post.content}</p>
                                <small class="post-meta">${t('published_on')}: ${post.published_at}</small>
                                <button class="view-button" data-id="${post.id}">${t('view_button')}</button>
                            `;
                            feedContainer.appendChild(postElement);
                        });
                        offset += posts.length;
                        loading = false;
                        loadingIndicator.style.display = 'none';
                        if (posts.length < limit) {
                            window.removeEventListener('scroll', handleScroll);
                        }
                    });
            } catch (error) {
                console.error('Error fetching posts:', error);
            }
        }

        function updateRecentlyViewed(viewed_posts) {
            recentlyViewedContainer.innerHTML = '';
            if (viewed_posts.length > 0) {
                viewed_posts.forEach(postId => {
                    const postElement = document.createElement('div');
                    postElement.innerText = `${t('viewed_post_id')}: ${postId}`;
                    recentlyViewedContainer.appendChild(postElement);
                });
            }
        }

        function fetchRecentlyViewed() {
            const viewed_posts = JSON.parse(getCookie('viewed_posts') || '[]');
            updateRecentlyViewed(viewed_posts);
        }

        function markAsRead(postId) {
            fetch('/api/mark_as_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: postId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const postElement = document.getElementById(`post-${postId}`);
                    if (postElement) {
                        postElement.remove();
                    }
                    updateRecentlyViewed(data.viewed_posts);
                }
            });
        }

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        feedContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('view-button')) {
                const postId = event.target.dataset.id;
                markAsRead(postId);
            }
        });

        function handleScroll() {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
                fetchPosts();
            }
        }

        function resetFeed() {
            offset = 0;
            feedContainer.innerHTML = '';
            fetchPosts();
        }

        sortOrderSelect.addEventListener('change', resetFeed);

        window.addEventListener('scroll', handleScroll);

        console.log('Calling fetchPosts for the first time');
        fetchPosts();
        fetchRecentlyViewed();
    });
</script>
</body>
</html>
