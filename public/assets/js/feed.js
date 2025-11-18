document.addEventListener('DOMContentLoaded', function () {
    const feedContent = document.querySelector('.feed-content');
    let isLoading = false;
    let page = 1;
    const initialLimit = 5;
    const subsequentLimit = 3;
    let viewedPosts = new Set(getCookie('viewed_posts')?.split(',') || []);

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

    function markPostAsViewed(postId) {
        if (!viewedPosts.has(postId)) {
            viewedPosts.add(postId);
            setCookie('viewed_posts', Array.from(viewedPosts).join(','), 365);
            const postElement = document.querySelector(`[data-post-id="${postId}"]`);
            if(postElement){
                postElement.classList.add('viewed');
            }
        }
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const postId = entry.target.dataset.postId;
                markPostAsViewed(postId);
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
                        if(viewedPosts.has(post.id)){
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

    loadPosts(initialLimit);

    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
            loadPosts(subsequentLimit);
        }
    });
});
