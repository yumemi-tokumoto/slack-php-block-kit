<?php

declare(strict_types=1);

namespace SlackPhp\BlockKit\Partials;

use SlackPhp\BlockKit\{Element, Exception, HydrationData};

class Filter extends Element
{
    private const CONVERSATION_TYPE_IM = 'im';
    private const CONVERSATION_TYPE_MPIM = 'mpim';
    private const CONVERSATION_TYPE_PRIVATE = 'private';
    private const CONVERSATION_TYPE_PUBLIC = 'public';

    /** @var string[]|array */
    private $include = [];

    /** @var bool */
    private $excludeExternalSharedChannels;

    /** @var bool */
    private $excludeBotUsers;

    /**
     * @param string $conversationType
     * @return static
     */
    public function includeType(string $conversationType)
    {
        $this->include[] = $conversationType;

        return $this;
    }

    /**
     * @param string[] $conversationTypes
     * @return static
     */
    public function includeTypes(array $conversationTypes)
    {
        $this->include = $conversationTypes;

        return $this;
    }

    /**
    * @return static
    */
    public function includeIm()
    {
        return $this->includeType(self::CONVERSATION_TYPE_IM);
    }

    /**
    * @return static
    */
    public function includeMpim()
    {
        return $this->includeType(self::CONVERSATION_TYPE_MPIM);
    }

    /**
    * @return static
    */
    public function includePrivate()
    {
        return $this->includeType(self::CONVERSATION_TYPE_PRIVATE);
    }

    /**
    * @return static
    */
    public function includePublic()
    {
        return $this->includeType(self::CONVERSATION_TYPE_PUBLIC);
    }

    /**
     * @param bool $excludeBotUsers
     * @return static
     */
    public function excludeBotUsers(bool $excludeBotUsers)
    {
        $this->excludeBotUsers = $excludeBotUsers;

        return $this;
    }

    /**
     * @param bool $excludeExternalSharedChannels
     * @return static
     */
    public function excludeExternalSharedChannels(bool $excludeExternalSharedChannels)
    {
        $this->excludeExternalSharedChannels = $excludeExternalSharedChannels;

        return $this;
    }

    public function validate(): void
    {
        if (empty($this->include) && !isset($this->excludeExternalSharedChannels) && !isset($this->excludeBotUsers)) {
            throw new Exception('Filter must have at least one property set');
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (!empty($this->include)) {
            $data['include'] = $this->include;
        }

        if (isset($this->excludeExternalSharedChannels)) {
            $data['exclude_external_shared_channels'] = $this->excludeExternalSharedChannels;
        }

        if (isset($this->excludeBotUsers)) {
            $data['exclude_bot_users'] = $this->excludeBotUsers;
        }

        return $data;
    }

    protected function hydrate(HydrationData $data): void
    {
        if ($data->has('include')) {
            $this->includeTypes($data->useArray('include'));
        }

        if ($data->has('exclude_external_shared_channels')) {
            $this->excludeExternalSharedChannels($data->useValue('exclude_external_shared_channels'));
        }

        if ($data->has('exclude_bot_users')) {
            $this->excludeBotUsers($data->useValue('exclude_bot_users'));
        }

        parent::hydrate($data);
    }
}
