<?php
/**
 * Created by PhpStorm.
 * User: MAV
 * Date: 19.02.2017
 * Time: 20:55
 */

ini_set('display_errors', true);
mb_internal_encoding('UTF-8');

//$delimiters has to be array
//$string has to be array

function multiexplode($delimiters, $string)
{

    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return $launch;
}

if (isset($_GET['id'])) {
    $articleId = (int)$_GET['id'];
    //web
    $content = file_get_contents('https://habrahabr.ru/post/' . $articleId . '/');
    //local
    //$content = file_get_contents('http://habrahabr:8080/post/'.$articleId.'/');
} else {
    $articleId = false;
    $content = false;
}
if ((substr_count($content, '<h1>Страница не найдена</h1>') === 0) and ($content !== false) and ($articleId !== false)) {


    $articleTitlePie = explode('<title>', $content);
    $articleTitlePie = explode('/', $articleTitlePie[1]);
    $articleTitle = trim(($articleTitlePie[0]));

    $articleFirstSentencePie = explode('<div class="content html_format">', $content);
    //$articleFirstSentencePie = explode('. ',$articleFirstSentencePie[1]);
    $articleFirstSentencePie = multiexplode(array('. ', '! ', '? '), $articleFirstSentencePie[1]);
    $articleFirstSentencePie = explode('<br>', $articleFirstSentencePie[0]);
    $articleFirstSentence = trim(strip_tags($articleFirstSentencePie[0]));


    $articleDatePie = explode('<span class="post__time_published">', $content);
    $articleDatePie = explode('</span>', $articleDatePie[1]);
    $articleDate = trim($articleDatePie[0]);

    $articleRatingPie = explode('title="Общий рейтинг ', $content);
    $articleRatingPie = explode('</span>', $articleRatingPie[1]);
    $articleRatingPie = explode('">', $articleRatingPie[0]);
    $articleRating = trim($articleRatingPie[1]);

    $articleViewsPie = explode('<div class="views-count_post" title="Просмотры публикации">', $content);
    $articleViewsPie = explode('</div>', $articleViewsPie[1]);
    $articleViews = trim($articleViewsPie[0]);

    $articleStarsPie = explode('title="Количество пользователей, добавивших публикацию в избранное">', $content);
    $articleStarsPie = explode('</span>', $articleStarsPie[1]);
    $articleStars = trim($articleStarsPie[0]);

    $articleTagsPie = explode('<h1 class="post__title">', $content);
    $articleTagsPie = explode('</h1>', $articleTagsPie[1]);
    $articleTagsPie = explode('title="', $articleTagsPie[0]);

    if (count($articleTagsPie) === 1 || count($articleTagsPie) === 0) {
        $articleTags = 'empty';
    } else {
        foreach ($articleTagsPie as $articleTagsId => $articleTagPie) {
            if ($articleTagsId === 0) {
                continue;
            } else {

                $articleTagPie = explode('">', $articleTagPie);

                $articleTagPie = explode('</', $articleTagPie[1]);

                $articleTags[] = trim($articleTagPie[0]);
            }
        }
    }

    if ($articleTags !== 'empty') {
        $articleFields = ['title' => $articleTitle, 'text' => $articleFirstSentence, 'date' => $articleDate, 'rating' => $articleRating, 'views' => $articleViews, 'stars' => $articleStars, 'tags' => $articleTags];
    } else {
        $articleFields = ['title' => $articleTitle, 'text' => $articleFirstSentence, 'date' => $articleDate, 'rating' => $articleRating, 'views' => $articleViews, 'stars' => $articleStars];
    }
} else {
    $articleFields = ['status' => 'error'];
}

echo json_encode($articleFields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);


