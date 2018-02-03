<?php

namespace Vega\Controller;

use Vega\Entity\Setting;
use Vega\Entity\Entity;
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

    /**
        * @param Entity $entity
    * @param int $amount
    */
    protected function incrementView(Entity $entity, int $amount = 1)
    {
        $entity->increment('views', $amount);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }

    protected function incrementVote(Entity $entity, int $amount = 1)
    {
        $entity->increment('votes', $amount);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }

    protected function decrementVote(Entity $entity, int $amount = 1)
    {
        $entity->decrement('votes', $amount);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }
}
