<?php

class translate
{
    protected $currentLang = 'th';

    protected $trans = array(
        'en' => array(
            'dashboard' => 'Dashboard',
            'topTravel' => 'Top 10 Traveler',
            'topKill' => 'Top 10 Killer'
        ),
        'th' => array(
            'dashboard' => 'หน้าบ้าน',
            'topTravel' => 'Top 10 นักเดินทาง',
            'topKill' => 'Top 10 มือสังหาร'
        )
    );

    public function getTranslate($keyword)
    {
        return (string)$this->trans[$this->currentLang][$keyword];
    }

    public function setCurrentLang($lang)
    {
        $this->currentLang = $lang;
    }

}

$trans = new translate();

//var_dump($trans->getTranslate('topTravel'));

?>
