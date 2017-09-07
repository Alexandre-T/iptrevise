<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 */

namespace App\Bean;

/**
 * Data class.
 *
 * @category App\Bean
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class Data
{
    /**
     * @var int Data Id
     */
    private $id = 0;
    /**
     * @var string entity name
     */
    private $entity = '';
    /**
     * @var string label code to be translated
     */
    private $label = 'settings.label';
    /**
     * @var string data name
     */
    private $name = '';
    /**
     * @var bool The dependency no more exists
     */
    private $noMore = false;
    /**
     * @var bool This data has no dependency (0::1 or 0::n relation)
     */
    private $none = false;
    /**
     * @var bool This data has to be translated
     */
    private $translate = false;

    /**
     * Getter of Id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Setter of Id.
     *
     * @param int $id
     *
     * @return Data
     */
    public function setId(int $id): Data
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Getter of entity.
     *
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * Setter of entity.
     *
     * @param string $entity
     *
     * @return Data
     */
    public function setEntity(string $entity): Data
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Getter of label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Setter of label.
     *
     * @param string $label
     *
     * @return Data
     */
    public function setLabel(string $label): Data
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Getter of name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setter of name.
     *
     * @param string $name
     *
     * @return Data
     */
    public function setName(string $name): Data
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Dependency exists or is no more existing?
     *
     * @return bool true when dependency does no more exists
     */
    public function isNoMore(): bool
    {
        return $this->noMore;
    }

    /**
     * Setter of no-more existing dependency.
     *
     * @param bool $noMore
     *
     * @return Data
     */
    public function setNoMore(bool $noMore): Data
    {
        $this->noMore = $noMore;

        return $this;
    }

    /**
     * Getter of translate.
     *
     * If true, Name has to be translated
     *
     * @return bool
     */
    public function getTranslate(): bool
    {
        return $this->translate;
    }

    /**
     * Setter of translator.
     *
     * @param bool $translate
     *
     * @return Data
     */
    public function setTranslate(bool $translate): Data
    {
        $this->translate = $translate;

        return $this;
    }

    /**
     * No dependency.
     *
     * @return bool
     */
    public function isNone(): bool
    {
        return $this->none;
    }

    /**
     * Setter of dependency.
     *
     * @param bool $none
     *
     * @return Data
     */
    public function setNone(bool $none): Data
    {
        $this->none = $none;

        return $this;
    }

    /**
     * This data had an id.
     *
     * @return bool
     */
    public function hasId()
    {
        return !empty($this->id);
    }

    /**
     * This data has a name.
     *
     * @return bool
     */
    public function hasName()
    {
        return !empty($this->name);
    }
}
