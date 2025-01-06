<?php
namespace app\controller;

use app\config\Constants;
use app\config\Urls;
use app\data\Input;
use app\data\Pagination;
use app\data\UrlsEnum;
use app\data\UserLevelsConstants;
use app\data\Validate;
use app\model\ForumModel;
use app\model\ForumPostModel;
use app\security\Session;

class ForumController extends \app\Controller {
    protected $_methodAccessPermissions = [
        'indexView' => [UserLevelsConstants::ALL],
        'newPostAction' => [UserLevelsConstants::REGISTER],
        'newTopicAction' => [UserLevelsConstants::REGISTER],
        'newTopicView' => [UserLevelsConstants::REGISTER],
        'showView' => [UserLevelsConstants::ALL],
        'topicView' => [UserLevelsConstants::ALL]
    ];

    public function indexView(): void {
        $this->_context['FORUM'] = ForumModel::getByParentForum(null);
        $this->_context['TOP_MEMBERS'] = ForumModel::getTopMessagesMembers(5);
        $this->_context['LATEST_POSTS'] = ForumModel::getLatestPosts(7);
        $this->_context['LATEST_TOPICS'] = ForumModel::getLatestTopics(7);
    }

    public function newPostAction(int $forum, int $topic): void {
        // TODO: Validar que el foro no esté cerrado, admita nuevos posts, tenga permisos el usuario...
        $forumTopic = ForumModel::getTopicById($topic);
        // Si el tema no existe o no está en el foro indicado, se redirige al usuario a la página de error.
        Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $forumTopic === null || $forumTopic->forum_id != $forum);
        // Se recupera el último POST que ha realizado el usuario autenticado.
        $latestPost = ForumModel::getUserLatestPost($this->_context['CLIENT']['LOGIN_USER']->id);
        // Se comprueba que el topic no esté cerrado y que haya transcurrido el tiempo necesario para publicar un nuevo post.
        if ($latestPost !== null && (strtotime($latestPost->post_published) * 1000 > time() * 1000 - Constants::FORUM_MIN_TIME_TO_POST_IN_SECONDS * 1000)) {
            Session::put(Constants::SESSION_ERROR_MESSAGES, [
                sprintf('Para enviar un nuevo mensaje tienes que esperar %s segundos desde tu última publicación.', Constants::FORUM_MIN_TIME_TO_POST_IN_SECONDS)
            ]);
        } else if($forumTopic->topic_is_closed) {
            Session::put(Constants::SESSION_ERROR_MESSAGES, [
                'Lo sentimos, pero este hilo está cerrado y no permite nuevos mensajes.'
            ]);
        } else {
            // Se validan los campos del formulario.
            $validation = new Validate($this->_context['CLIENT']['IP'], 'ForumController-topicView', $this->_context['CLIENT']['BROWSER']);
            $validation->check($_POST, [
                'content' => [ 'label' => LANG_CONTENT, 'max' => 65535, 'min' => 10, 'required' => true],
            ], true, false);
            if(!$validation->passed()) {
                Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
            } else {
                $insert = ForumPostModel::insert(
                    "RE: {$forumTopic->topic_title}",
                    Input::getPost('content'),
                    $this->_context['CLIENT']['LOGIN_USER']->id,
                    $forum,
                    $topic,
                    false
                );
                if(!$insert) {
                    Input::cleanPost('content');
                    Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'El post se ha publicado correctamente.');
                } else {
                    Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
                }
            }
        }
        // Redirección.
        Urls::redirectTo(UrlsEnum::FORUM_VIEW_SHOWTOPIC, [$forum, $topic, 1]);
    }

    public function newTopicAction(int $forum): void {
        // TODO: Validar que el foro exista, no esté cerrado, admita nuevos temas, tenga permisos el usuario...
    }
    public function newTopicView(int $forum): void {
        $this->_context['FORUM'] = ForumModel::getById($forum);
        // Si el foro no existe, se redirige al usuario a la página de error.
        Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['FORUM'] === null);
    }

    public function showView(int $id, int $page): void {
        $this->_context['FORUM'] = ForumModel::getById($id);
        // Si el foro no existe, se redirige al usuario a la página de error.
        Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['FORUM'] === null);
        // Se prepara el contexto.
        $this->_context['FORUMS'] = ForumModel::getByParentForum($id);
        $this->_context['FORUM_TOPICS'] = ForumModel::getLimitTopics($id, ($page - 1) * Constants::ELEMENTS_PER_PAGE, Constants::ELEMENTS_PER_PAGE);
        $this->_context['TOP_MEMBERS'] = ForumModel::getTopMessagesMembers(5);
        $this->_context['LATEST_POSTS'] = ForumModel::getLatestPosts(7);
        $this->_context['LATEST_TOPICS'] = ForumModel::getLatestTopics(7);
        $this->_context['FORUM_TOPICS_TOTAL'] = ForumModel::getTotalTopicsByForum($id);
        $this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['FORUM_TOPICS'], $page, $this->_context['FORUM_TOPICS_TOTAL'], UrlsEnum::FORUM_VIEW_SHOW, Constants::ELEMENTS_PER_PAGE, [$id]);
    }

    public function topicView(int $forum, int $id, int $page): void {
        $this->_context['TOPIC'] = ForumModel::getTopicById($id);
        // Si el tema no existe, se redirige al usuario a la página de error.
        Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['TOPIC'] === null);
        // Se prepara el contexto.
        $this->_context['TOPIC_POSTS'] = ForumModel::getTopicLimitPosts($id, ($page - 1) * Constants::ELEMENTS_PER_PAGE, Constants::ELEMENTS_PER_PAGE);
        $this->_context['TOPIC_POSTS_TOTAL'] = ForumModel::getTotalTopicPosts($id);
        $this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['TOPIC_POSTS'], $page, $this->_context['TOPIC_POSTS_TOTAL'], UrlsEnum::FORUM_VIEW_SHOWTOPIC, Constants::ELEMENTS_PER_PAGE, [$id]);
        $this->_context['FORMDATA'] = [
            'CONTENT' => empty(Session::get(Constants::SESSION_SUCCESS_MESSAGES)) ? Input::getPost('content') : ''
        ];
    }
}
