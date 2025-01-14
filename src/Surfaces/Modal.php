<?php

declare(strict_types=1);

namespace SlackPhp\BlockKit\Surfaces;

use SlackPhp\BlockKit\{
    Exception,
    HydrationData,
    Partials\PlainText,
    Type,
};

/**
 * Modals provide focused spaces ideal for requesting and collecting data from users, or temporarily displaying dynamic
 * and interactive information.
 *
 * @see https://api.slack.com/surfaces
 */
class Modal extends View
{
    private const MAX_LENGTH_TITLE = 24;

    /** @var PlainText */
    private $title;

    /** @var PlainText */
    private $submit;

    /** @var PlainText */
    private $close;

    /** @var bool */
    private $clearOnClose;

    /** @var bool */
    private $notifyOnClose;

    /**
    * @return static
    */
    public function setTitle(PlainText $title)
    {
        $this->title = $title->setParent($this);

        return $this;
    }

    /**
    * @return static
    */
    public function setSubmit(PlainText $title)
    {
        $this->submit = $title->setParent($this);

        return $this;
    }

    /**
    * @return static
    */
    public function setClose(PlainText $title)
    {
        $this->close = $title->setParent($this);

        return $this;
    }

    /**
    * @return static
    */
    public function title(string $title)
    {
        return $this->setTitle(new PlainText($title));
    }

    /**
    * @return static
    */
    public function submit(string $submit)
    {
        return $this->setSubmit(new PlainText($submit));
    }

    /**
    * @return static
    */
    public function close(string $close)
    {
        return $this->setClose(new PlainText($close));
    }

    /**
    * @return static
    */
    public function clearOnClose(bool $clearOnClose)
    {
        $this->clearOnClose = $clearOnClose;

        return $this;
    }

    /**
    * @return static
    */
    public function notifyOnClose(bool $notifyOnClose)
    {
        $this->notifyOnClose = $notifyOnClose;

        return $this;
    }

    public function validate(): void
    {
        parent::validate();

        if (empty($this->title)) {
            throw new Exception('Modals must have a "title"');
        }
        $this->title->validateWithLength(self::MAX_LENGTH_TITLE);

        $hasInputs = false;
        foreach ($this->getBlocks() as $block) {
            if ($block->getType() === Type::INPUT) {
                $hasInputs = true;
                break;
            }
        }
        if ($hasInputs && empty($this->submit)) {
            throw new Exception('Modals must have a "submit" button defined if they contain any "input" blocks');
        }
    }

    public function toArray(): array
    {
        $data = [];

        $data['title'] = $this->title->toArray();

        if (!empty($this->submit)) {
            $data['submit'] = $this->submit->toArray();
        }

        if (!empty($this->close)) {
            $data['close'] = $this->close->toArray();
        }

        if (!empty($this->clearOnClose)) {
            $data['clear_on_close'] = $this->clearOnClose;
        }

        if (!empty($this->notifyOnClose)) {
            $data['notify_on_close'] = $this->notifyOnClose;
        }

        $data += parent::toArray();

        return $data;
    }

    protected function hydrate(HydrationData $data): void
    {
        if ($data->has('title')) {
            $this->setTitle(PlainText::fromArray($data->useElement('title')));
        }

        if ($data->has('submit')) {
            $this->setSubmit(PlainText::fromArray($data->useElement('submit')));
        }

        if ($data->has('close')) {
            $this->setClose(PlainText::fromArray($data->useElement('close')));
        }

        if ($data->has('clear_on_close')) {
            $this->clearOnClose($data->useValue('clear_on_close'));
        }

        if ($data->has('notify_on_close')) {
            $this->notifyOnClose($data->useValue('notify_on_close'));
        }

        parent::hydrate($data);
    }
}
