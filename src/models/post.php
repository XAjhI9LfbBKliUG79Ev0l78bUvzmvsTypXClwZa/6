<?php
class Post {
    public static function get_all() {
        $posts_dir = __DIR__ . '/../../posts';
        $files = scandir($posts_dir, SCANDIR_SORT_DESCENDING);

        $posts = [];
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $filepath = $posts_dir . '/' . $file;
            $data = file_get_contents($filepath);
            $post_data = explode(';', $data, 5);

            if (count($post_data) !== 5) {
                continue;
            }

            list($uuid, $title, $content, $post_time, $viewed_state) = $post_data;

            if (empty($uuid) || empty($title) || empty($content) || empty($post_time)) {
                continue;
            }

            $posts[] = [
                'id' => $uuid,
                'title' => $title,
                'content' => $content,
                'published_at' => $post_time,
                'viewed' => (bool)$viewed_state
            ];
        }

        return $posts;
    }

    public static function get_by_ids($ids) {
        if (empty($ids)) {
            return [];
        }

        $all_posts = self::get_all();
        $found_posts = [];

        foreach ($all_posts as $post) {
            if (in_array($post['id'], $ids)) {
                $found_posts[$post['id']] = [
                    'id' => $post['id'],
                    'title' => $post['title'],
                ];
            }
        }

        $sorted_posts = [];
        foreach ($ids as $id) {
            if (isset($found_posts[$id])) {
                $sorted_posts[] = $found_posts[$id];
            }
        }

        return $sorted_posts;
    }
}
