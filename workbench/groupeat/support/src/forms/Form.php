<?php namespace Groupeat\Support\Forms;

use App;
use Lang;
use Session;

abstract class Form {

    /**
     * @var \Former\Former
     */
    protected $former;

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $httpVerb = 'POST';

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var string
     */
    protected $content = '';


    public function __construct()
    {
        $this->former = App::make('former');
    }

    /**
     * @return string
     */
    abstract protected function add($content);

    /**
     * @return string
     */
    public function render()
    {
        $this->open();
        $this->content .= $this->add('');
        $this->close();

        return $this->getError() . $this->content;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    public function getError()
    {
        if ($errorBags = Session::get('errors'))
        {
            if ($errorBags->hasBag('default'))
            {
                $bag = $errorBags->getBag('default');

                if ($bag->has('error'))
                {
                    return '<div class="alert alert-warning" role="alert">'.$bag->first('error').'</div>';
                }
            }
        }
    }

    protected function password($name = 'password', $placeholder = null)
    {
        return $this->text($name, $placeholder)->type('password')->forceValue('');
    }

    protected function text($name, $placeholder = null)
    {
        $field = $this->former->text($name);

        if ($placeholder == null)
        {
            $field->placeholder($name);
        }

        return $field;
    }

    protected function submit($action, $class = 'primary')
    {
        return $this->former->button(Lang::get("validation.attributes.$action"))
            ->class('btn btn-lg btn-block btn-'.$class)
            ->type('submit');
    }

    protected function open()
    {
        $this->content = $this->former
            ->open()
            ->method('POST')
            ->id($this->id)
            ->rules($this->getRules());

        $this->content .= $this->former->hidden('_method', $this->httpVerb);
    }

    protected function close()
    {
        $this->content .= $this->former->close();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}
