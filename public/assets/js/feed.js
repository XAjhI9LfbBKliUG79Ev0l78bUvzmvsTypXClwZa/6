document.addEventListener('DOMContentLoaded', function () {
    const feedContent = document.querySelector('.feed-content');
    const viewedPostsList = document.querySelector('.viewed-posts ul');
    let isLoading = false;
    let page = 1;
    const initialLimit = 5;
    const subsequentLimit = 3;
    const VIEWED_POSTS_LIMIT = 10;
    let viewedPosts = getCookie('viewed_posts')?.split(',').filter(Boolean) || [];

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

    function loadViewedPostsHistory() {
        if (viewedPosts.length > 0 && viewedPostsList) {
            fetch(`/api/get_post_details.php?ids=${viewedPosts.join(',')}`)
                .then(response => response.json())
                .then(posts => {
                    viewedPostsList.innerHTML = '';
                    posts.forEach(post => {
                        const listItem = document.createElement('li');
                        listItem.textContent = post.title;
                        viewedPostsList.appendChild(listItem);
                    });
                })
                .catch(error => {
                    console.error('Error loading viewed posts history:', error);
                });
        }
    }

    function markPostAsViewed(postElement) {
        const postId = postElement.dataset.postId;

        if (viewedPosts[0] === postId) {
            return;
        }

        const existingIndex = viewedPosts.indexOf(postId);
        if (existingIndex > -1) {
            viewedPosts.splice(existingIndex, 1);
        }

        viewedPosts.unshift(postId);

        if (viewedPosts.length > VIEWED_POSTS_LIMIT) {
            viewedPosts.pop();
        }

        setCookie('viewed_posts', viewedPosts.join(','), 365);
        postElement.classList.add('viewed');
        loadViewedPostsHistory();
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                markPostAsViewed(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });


    function loadPosts(limit) {
        if (isLoading) return;
        isLoading = true;

        fetch(`/api/get_posts.php?page=${page}&limit=${limit}`)
            .then(response => response.json())
            .then(posts => {
                if (posts.length > 0) {
                    posts.forEach(post => {
                        const article = document.createElement('article');
                        article.classList.add('post');
                        article.dataset.postId = post.id;
                        if(viewedPosts.includes(post.id)){
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

                        feedContent.appendChild(article);
                        observer.observe(article);
                    });
                    page++;
                }
                isLoading = false;
            })
            .catch(error => {
                console.error('Error loading posts:', error);
                isLoading = false;
            });
    }

    loadViewedPostsHistory();
    loadPosts(initialLimit);

    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
            loadPosts(subsequentLimit);
        }
    });
});
