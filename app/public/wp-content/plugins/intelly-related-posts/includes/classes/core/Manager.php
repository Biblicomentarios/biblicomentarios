<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class IRP_Manager {

    public function __construct() {

    }

    public function getRelatedPostsIds($args) {
        global $irp;

        $ids=array();
        if(IRP_DISABLE_RELATED || !$irp->Options->isActive()) {
            return $ids;
        }

        $defaults=array('postId'=>0
            , 'posts'=>array(), 'cats'=>array(), 'tags'=>array()
            , 'count'=>10
            , 'shuffle'=>FALSE);
        $args=$irp->Utils->parseArgs($args, $defaults);

        $args['posts']=$irp->Utils->toCommaArray($args['posts']);
        $args['cats']=$irp->Utils->toCommaArray($args['cats']);
        $args['tags']=$irp->Utils->toCommaArray($args['tags']);
        $args['postId']=intval($args['postId']);
        if($args['postId']>0) {
            $options=array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids');
            $args['tags']=wp_get_post_tags($args['postId'], $options);
            $args['cats']=wp_get_post_categories($args['postId']);
        }

        if(count($args['posts'])>0) {
            $ids=$args['posts'];
        } else {
            $ids=array();
            switch ($irp->Options->getEngineSearch()) {
                case IRP_ENGINE_SEARCH_CATEGORIES:
                    $ids=$this->queryRelatedPostsIds($args, 'category__in', $args['cats']);
                    break;
                case IRP_ENGINE_SEARCH_TAGS:
                    $ids=$this->queryRelatedPostsIds($args, 'tag__in', $args['tags']);
                    break;
                case IRP_ENGINE_SEARCH_CATEGORIES_TAGS:
                    //if we execute this query with the 2 parameters togheter category__in
                    //and tag__in WP do an AND query and not an OR query as we need
                    $cats=$this->queryRelatedPostsIds($args, 'category__in', $args['cats']);
                    $tags=$this->queryRelatedPostsIds($args, 'tag__in', $args['tags']);
                    $ids=array_merge($cats, $tags);
                    $ids=array_unique($ids);
                    break;
            }
        }

        if($args['shuffle']) {
            shuffle($ids);
        }
        return $ids;
    }

    private function queryRelatedPostsIds($args, $optionKey, $optionArray) {
        global $irp;

        $ids=array();
        if(!is_array($optionArray) || count($optionArray)==0) {
            return $ids;
        }

        $post=$irp->Options->getPostShown();
        $options=array(
            'post_type'=>$post->post_type
            //, 'nopaging'=>TRUE
            , 'posts_per_page'=>10
            , 'post_status'=>'publish'
            , 'post__not_in'=>array($post->ID)
            , 'orderby'=>'rand'
        );
        $days=$irp->Options->getRewritePostsDays();
        if($days>0) {
            $options['date_query'] = array(
                'column' => 'post_date'
                , 'after' => '- '.$days.' days'
            );
        }
        $options[$optionKey]=$optionArray;

        $q=new WP_Query();
        $irp->Log->debug('RELATED POSTS QUERY=%s', $options);
        $posts=$q->query($options);

        $irp->Log->debug('RELATED POSTS RESULT COUNT=%s', count($posts));
        foreach($posts as $p) {
            if($args['postId']<=0 || $args['postId']!=$p->ID) {
                $ids[]=$p->ID;
            }
        }
        return $ids;
    }
}