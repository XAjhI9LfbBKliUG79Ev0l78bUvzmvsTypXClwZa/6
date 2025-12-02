window.addEventListener('load', function () {
    const feedContent = document.querySelector('.feed-content');
    const viewedPostsList = document.querySelector('.viewed-posts-dropdown ul');
    let isLoading = false;
    let page = 1;
    const initialLimit = 10;
    const subsequentLimit = 10;
    const VIEWED_POSTS_LIMIT = 10;

    let isLoggedIn = false;
    let viewedPostsCookie = []; // For anonymous users

    // --- Cookie helpers for anonymous users ---
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function createViewedPostElement(post) {
        const listItem = document.createElement('li');
        listItem.classList.add('post');

        const title = document.createElement('h4');
        title.textContent = post.title;

        const meta = document.createElement('small');
        meta.textContent = `Published on: ${post.published_at}`;

        listItem.appendChild(title);
        listItem.appendChild(meta);
        return listItem;
    }

    // --- History loading logic ---
    function loadViewedPostsHistory() {
        let postIdsPromise;

        if (isLoggedIn) {
            // Logged-in: fetch from server
            postIdsPromise = fetch('/api/posts.php?action=get_viewed_post_ids')
                .then(response => {
                    if (!response.ok) return [];
                    return response.json();
                });
        } else {
            // Anonymous: use cookies
            postIdsPromise = Promise.resolve(viewedPostsCookie);
        }

        postIdsPromise.then(postIds => {
            if (postIds.length > 0) {
                fetch(`/api/posts.php?action=get_post_details&ids=${postIds.join(',')}`)
                    .then(response => response.json())
                    .then(posts => {
                        viewedPostsList.innerHTML = ''; // Clear previous entries
                        posts.forEach(post => {
                            const postElement = createViewedPostElement(post);
                            viewedPostsList.appendChild(postElement);
                        });
                    }).catch(error => console.error('Error fetching post details:', error));
            } else if (viewedPostsList) {
                viewedPostsList.innerHTML = '<li>No viewed posts yet.</li>';
            }
        }).catch(error => console.error('Error resolving post IDs:', error));
    }

    // --- Post viewing logic ---
    function markPostAsViewed(postElement) {
        const postId = postElement.dataset.postId;
        if (postElement.classList.contains('viewed')) return;

        postElement.classList.add('viewed');
        loadViewedPostsHistory();

        if (isLoggedIn) {
            // Logged-in: send to server
            fetch('/api/posts.php?action=mark_post_as_viewed', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ postId: postId })
            }).then(response => {
                if (response.ok) {
                    loadViewedPostsHistory();
                }
            }).catch(error => console.error('Error marking post as viewed:', error));
        } else {
            // Anonymous: update cookie
            const existingIndex = viewedPostsCookie.indexOf(postId);
            if (existingIndex > -1) {
                viewedPostsCookie.splice(existingIndex, 1);
            }
            viewedPostsCookie.unshift(postId);
            if (viewedPostsCookie.length > VIEWED_POSTS_LIMIT) {
                viewedPostsCookie.pop();
            }
            setCookie('viewed_posts', viewedPostsCookie.join(','), 365);
            loadViewedPostsHistory();
        }
    }

    // --- Intersection Observer ---
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                markPostAsViewed(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    // --- Infinite scroll loader ---
    function createPostElement(post) {
        const article = document.createElement('article');
        article.classList.add('post');
        article.dataset.postId = post.id;
        if (post.viewed) {
            article.classList.add('viewed');
        }

        const title = document.createElement('h2');
        title.textContent = post.title;
        const content = document.createElement('p');
        content.innerHTML = post.content;
        const meta = document.createElement('small');
        meta.classList.add('post-meta');
        meta.textContent = `Published on: ${post.published_at}`;

        article.appendChild(title);
        article.appendChild(content);
        article.appendChild(meta);

        return article;
    }

    function loadPosts(limit) {
        if (isLoading) return;
        isLoading = true;

        fetch(`/api/posts.php?action=get_posts&page=${page}&limit=${limit}`)
            .then(response => response.json())
            .then(posts => {
                if (posts.length > 0) {
                    posts.forEach(post => {
                        const postElement = createPostElement(post);
                        feedContent.appendChild(postElement);
                        if (!post.viewed) {
                            observer.observe(postElement);
                        }
                    });
                    page++;
                } else {
                    loadMoreButton.style.display = 'none'; // Hide button if no more posts
                }
                isLoading = false;
            })
            .catch(error => {
                console.error('Error loading posts:', error);
                isLoading = false;
            });
    }

    // --- Initialization ---
    const loadMoreButton = document.createElement('button');
    function initializeFeed() {
        if (typeof window.isUserLoggedIn !== 'undefined') {
            isLoggedIn = window.isUserLoggedIn;
        }

        if (isLoggedIn) {
            loadViewedPostsHistory();
            loadPosts(initialLimit);
        } else {
            viewedPostsCookie = getCookie('viewed_posts')?.split(',').filter(Boolean) || [];
            loadViewedPostsHistory();
            loadPosts(initialLimit);
        }

        loadMoreButton.textContent = 'Load More';
        loadMoreButton.addEventListener('click', () => loadPosts(subsequentLimit));
        feedContent.insertAdjacentElement('afterend', loadMoreButton);
    }

    initializeFeed();
});
