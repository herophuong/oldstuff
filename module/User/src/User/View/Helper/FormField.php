<?php
namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Form;

class FormField extends AbstractHelper
{
    /**
     * Helper for display a form field
     * 
     * @param Form $form The form object
     * @param string $field The field name 
     * @param string $layout Specify the column layout (label-input) for the field
     *
     * @return $string The html for the field
     */
    public function __invoke(Form $form, $field, $layout = '4-8')
    {
        $html = '<div class="form-group">';
        $layout = explode('-', $layout);
        if (count($layout) !== 2)
            throw new InvalidArgumentException('Invalid layout provided!');
        
        $element = $form->get($field);
        $element->setLabelAttributes(array('class' => 'control-label col-lg-'. (int) $layout[0]));
        $element->setAttribute('class', 'form-control');
        
        $messages = $form->getMessages();
        
        $html .= $this->getView()->formLabel($element);
        $html .= '<div class="col-lg-' . (int) $layout[1] . '">';
        $html .= $this->getView()->formInput($element);
        if (isset($messages[$field]))
            $html .= '<br /><div class="alert alert-danger">' . implode('<br />', $messages[$field]) . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}