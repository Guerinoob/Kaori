<?php
/**
 * FormBuilder class
 */

 namespace App;

 //TODO : Container in addField for specifics containers 

/**
 * This class is used to construct forms dynamically. Different fields can be added, and the form can be rendered as an HTML form
 */
class FormBuilder {    
    /**
     * fields
     *
     * @var array
     */
    protected $fields;
    
    /**
     * The URL or path that will handle the submission
     *
     * @var string
     */
    protected $action;
    
    /**
     * The URL or path that will handle the submission
     *
     * @var string
     */
    protected $method;

    /**
     * Default HTML wrapper for every field. Must contain a {{field}} string
     *
     * @var string
     */
    protected $field_container;
    
    /**
     * Constructor - Does nothing special
     *
     * @return void
     */
    public function __construct()
    {
        $this->fields = [];
        $this->action = '';
        $this->method = '';
        $this->field_container = '<div class="form-group">{{field}}</div>';
    }

    /**
     * Returns the URL or path that will handle the submission
     * 
     * @return string The URL or path that will handle the submission
     */ 
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Sets the URL or path that will handle the submission
     *
     * @param string The URL or path that will handle the submission
     * @return  self The instance of the FormBuilder
     */ 
    public function setAction($action): FormBuilder
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Returns the HTTP request's method of the submission
     * 
     * @return string The HTTP request's method of the submission
     */  
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Sets the HTTP request's method of the submission
     *
     * @param string The HTTP request's method of the submission
     * @return  self The instance of the FormBuilder
     */ 
    public function setMethod($method): FormBuilder
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Returns the default HTML wrapper for every field
     * 
     * @return string The default HTML wrapper for every field
     */  
    public function getFieldContainer(): string
    {
        return $this->method;
    }

    /**
     * Sets the default HTML wrapper for every field. Must contain a {{field}} string where the field will be rendered
     * 
     * @param string The default HTML wrapper for every field
     * @return FormBuilder|null Returns the current instance if the html string is correct (contains a "{{field}}" string), null otherwise
     */  
    public function setFieldContainer($html): ?FormBuilder
    {
        if(!preg_match('/{{field}}/', $html))
            return null;

        $this->field_container = $html;
    }
    
    /**
     * Adds a field to the form's fields
     *
     * @param  mixed $params An array describing the field to add<br>
     *               The array can contain these values :
     *               - type => string -> input, password, date, select, radio, checkbox, fieldset, fieldset_close, html, button, submit button
     *               - label => string -> the label of the field / the field group
     *               - value => string|array -> a string for a simple field, or an associative array for more complex ones like selects, radios or checkboxes. The key is the value and the value is an array with the keys label and selected
     *               - id => string
     *               - name => string
     *               - extra => array -> an associative array where the key is the attribute name and the value the attribute value
     *               - class => string
     * @return FormBuilder|null Returns the current instance if the field was added, null otherwise
     */
    public function addField($params): ?FormBuilder
    {
        if(!is_array($params))
            return null;

        $default_params = [
            'type' => '',
            'label' => '',
            'value' => '',
            'id' => '',
            'name' => '',
            'extra' => [],
            'class' => '',
            'selected' => false,
        ];

        $params = array_merge($default_params, $params);

        switch($params['type']) {
            case 'select':
                if(!is_array($params['value']))
                    return null;

                break;

            case 'radio':
                if(!is_array($params['value']))
                    return null;

                break;

            case 'checkbox':
                if(!is_array($params['value']))
                    return null;

                break;

            case 'fieldset':
                break;

            case 'html':
                break;

            case 'button':
                break;
        }

        $this->fields[] = $params;

        return $this;
    }
    
    /**
     * Renders the form in HTML
     * 
     * The form gives a protection against CSRF attacks by providing an input field "token" that contains the token in the session.<br>
     * In order to ensure the protection against this type of attacks, check that the submitted token matches the token in the session.
     *
     * @param  bool $echo If true, the form HTML will be printed, else the HTML string will be returned
     * @return string|null Returns a HTML string of the form if the parameter echo is true, null otherwise
     */
    public function render($echo = true): ?string
    {
        if(count($this->fields) == 0)
            return '';

        $this->generateCsrfToken();

        $form = '<form method="'.$this->method.'" action="'.$this->action.'">';
        $form .= '<input type="hidden" name="csrf" value="'.$_SESSION['csrf'].'">';

        $fieldset_open = false;

        foreach($this->fields as $field) {
            $html = '';
            $id = '';
            $class = '';

            if(is_array($field['extra'])) {
                $extra = array_reduce(array_keys($field['extra']), function($total, $current) use($field) {
                    return $total.' '.$current.'="'.$field['extra'][$current].'" ';
                }, '');
            }
            else {
                $extra = '';
            }

            if(!empty($field['class']))
                $class = 'class="'.$field['class'].'"';

            if(!empty($field['id']))
                $id = 'id="'.$field['id'].'"';

            switch($field['type']) {
                case 'select':
                    $html .= '<label for="'.$field['id'].'">'.$field['label'].'</label>';
                    $html .= '<select '.$class.' '.$id.' name="'.$field['name'].'" '.$extra.'>';

                    $options = array_reduce(array_keys($field['value']), function($total, $current) use($field) {
                        $selected = $field['value'][$current]['selected'] ? 'selected' : '';
                        return $total.'<option value="'.$current.'" '.$selected.'>'.$field['value'][$current]['label'].'</option>';
                    }, '');

                    $html .= $options;

                    $html .= '</select>';
                    break;
    
                case 'radio':
                    $html .= '<p>'.$field['label'].'</p>';

                    $inputs = array_reduce(array_keys($field['value']), function($total, $current) use($field, $extra, $class) {
                        $id = 'id="'.$field['id'].'_'.$current.'"';
                        $selected = $field['value'][$current]['selected'] ? 'checked' : '';

                        return $total.'<label for="'.$field['name'].'_'.$current.'">'.$field['value'][$current]['label'].'</label><input type="radio" '.$class.' name="'.$field['name'].'" '.$id.' value="'.$current.'" '.$extra.' '.$selected.'>';
                    }, '');

                    $html .= $inputs;
                    
                    break;
    
                case 'checkbox':
                    $html .= '<p>'.$field['label'].'</p>';

                    $inputs = array_reduce(array_keys($field['value']), function($total, $current) use($field, $extra, $class) {
                        $id = 'id="'.$field['id'].'_'.$current.'"';
                        $selected = $field['value'][$current]['selected'] ? 'checked' : '';

                        return $total.'<label for="'.$field['name'].'_'.$current.'">'.$field['value'][$current]['label'].'</label><input type="checkbox" '.$class.' name="'.$field['name'].'[]" '.$id.' value="'.$current.'" '.$extra.' '.$selected.'>';
                    }, '');

                    $html .= $inputs;
                    break;
    
                case 'fieldset':
                    if($fieldset_open)
                        $html .= '</fieldset>';

                    $html .= '<fieldset '.$class.' '.$id.' '.$extra.'>';
                    $html .= '<legend>'.$field['value'].'</legend>';

                    $fieldset_open = true;
                    break;
    
                case 'html':
                    $html .= $field['value'];                    
                    break;
    
                case 'button':
                    $html .= '<button type="button" '.$class.' '.$id.' name="'.$field['name'].'" value="'.$field['value'].'" '.$extra.'>'.$field['label'].'</button>';
                    break;

                case 'submit':
                    $html .= '<button type="submit" '.$class.' '.$id.' name="'.$field['name'].'" value="'.$field['value'].'" '.$extra.'>'.$field['label'].'</button>';
                    break;

                case 'fieldset_close':
                    if($fieldset_open)
                        $html .= '</fieldset>';

                    $fieldset_open = false;
                    break;

                default:
                    $html .= '<label for="'.$field['id'].'">'.$field['label'].'</label>';
                    $html .= '<input type="'.$field['type'].'" '.$class.' '.$id.' name="'.$field['name'].'" value="'.$field['value'].'" '.$extra.'>';
                    break;
            }

            if(!in_array($field['type'], ['fieldset', 'fieldset_close', 'html']))
                $html = str_replace('{{field}}', $html, $this->field_container);
            
            $form .= $html;
        }

        if($fieldset_open)
            $form .= '</fieldset>';

        $form .= '</form>';

        if($echo) {
            echo $form;
            return null;
        }

        return $form;
    }
    
    /**
     * Generates a token which is then stored in the session, used to prevent CSRF attacks
     *
     * @return void
     */
    private function generateCsrfToken(): void
    {
        $_SESSION['csrf'] = bin2hex(\random_bytes(32));
    }

}