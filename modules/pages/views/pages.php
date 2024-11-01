<?php
class pagesViewWcu extends viewWcu {
    public function displayDeactivatePage() {
        $this->assign('GET', reqWcu::get('get'));
        $this->assign('POST', reqWcu::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(reqWcu::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(reqWcu::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}

