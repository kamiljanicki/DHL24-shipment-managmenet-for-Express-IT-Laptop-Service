<?php

class finderView extends view
{
    public function find($post)
    {
        $model = $this -> loadModel('finder');
        $this -> set('finderResults', $model -> find($post));
        $this -> set('phrase', $post['finder_phrase']);
        $this -> set('findBy', $post['search_by']);
        $this -> render('finderResults');
    }
}