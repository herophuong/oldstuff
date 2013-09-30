<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class DeleteConfirmationScript extends AbstractHelper
{
    public function __invoke()
    {
        $this->getView()->headScript()->appendFile($this->getView()->basePath() . '/js/bootbox.min.js');
        $script = '
            $(document).ready(function(){
                $("a.delete-link").on("click", function() {
                    var $link = $(this);
                    bootbox.confirm("<i class=\'iconbig-triangle\'></i>Do you really want to delete this?", function(result) {
                        if (result == true)
                            window.location.href = $link.attr("href");
                    });
                    return false;
                });
            });
        ';
        $this->getView()->headScript()->appendScript($script);
    }
}