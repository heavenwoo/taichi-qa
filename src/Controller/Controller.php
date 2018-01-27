<?php

namespace App\Controller;

use App\Entity\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class Controller extends BaseController
{
    /**
     * @Cache(expires="1 week")
     * @return array
     */
    public function getSettings(): array
    {
        $settings = $this->getDoctrine()->getRepository(Setting::class)->findAll();

        /* @var Setting $setting*/
        foreach ($settings as $setting) {
            $settingArray[$setting->getName()] = $setting->getValue();
        }

        return $settingArray;
    }
}
