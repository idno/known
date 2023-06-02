<?php


namespace Idno\Entities;

/**
 * Define entities that can be mutated into other entities.
 */
interface Mutable
{

    /**
     * Mutate a class into one of its parents or children.
     * This is inherently risky, but sometimes it's useful to be able to do, for example
     * when a RemoteUser becomes a regular local user. So that we keep their history, they need to be
     * "mutated".
     *
     * @param  string $targetClass The class name and namespace
     * @return \Idno\Common\Entity|null
     */
    public function mutate(string $targetClass): ? \Idno\Common\Entity;
}
