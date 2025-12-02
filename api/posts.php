<?php
require_once __DIR__ . '/../src/includes/api.php';
require_once __DIR__ . '/../src/models/post.php';

$action = $_GET['action'] ?? null;

switch ($action) {
    case 'get_posts':
        $all_posts = Post::get_all();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        $offset = ($page - 1) * $limit;

        $paginated_posts = array_slice($all_posts, $offset, $limit);

        $viewed_post_ids = [];
        if ($user) {
            $viewed_post_ids = $user->viewed_posts;
        } else {
            $viewed_post_ids = isset($_COOKIE['viewed_posts']) ? explode(',', $_COOKIE['viewed_posts']) : [];
        }

        foreach ($paginated_posts as &$post) {
            $post['viewed'] = in_array($post['id'], $viewed_post_ids);
        }
        unset($post); // Unset reference

        echo json_encode($paginated_posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        break;
    case 'get_post_details':
        $post_ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
        $posts = Post::get_by_ids($post_ids);
        echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        break;
    case 'get_viewed_post_ids':
        if (!$user) {
            http_response_code(401);
            exit();
        }

        echo json_encode($user->viewed_posts);
        break;
    case 'mark_post_as_viewed':
        if (!$user) {
            http_response_code(401);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $post_id = $data['postId'] ?? null;

        if (!$post_id) {
            http_response_code(400);
            exit();
        }

        // Remove the post ID if it already exists to avoid duplicates
        $user->viewed_posts = array_diff($user->viewed_posts, [$post_id]);
        // Prepend the new post ID to the beginning of the array
        array_unshift($user->viewed_posts, $post_id);
        // Enforce a limit on the number of viewed posts
        $user->viewed_posts = array_slice($user->viewed_posts, 0, 10);
        $user->save();

        http_response_code(200);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
