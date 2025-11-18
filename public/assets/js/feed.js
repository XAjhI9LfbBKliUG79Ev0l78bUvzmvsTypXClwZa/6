document.addEventListener('DOMContentLoaded', function () {
    const feedContent = document.querySelector('.feed-content');
    let isLoading = false;
    let postCount = 10;

    function loadPosts() {
        if (isLoading) return;
        isLoading = true;

        fetch(`/api/generator.php?count=5`)
            .then(response => response.json())
            .then(posts => {
                posts.forEach(post => {
                    const article = document.createElement('article');
                    article.classList.add('post');

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
                });

                postCount += posts.length;
                isLoading = false;
            })
            .catch(error => {
                console.error('Error loading posts:', error);
                isLoading = false;
            });
    }

    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
            if (postCount < 10) {
                loadPosts();
            }
        }
    });
});
