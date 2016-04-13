<?php

namespace Nz\MigrationBundle\Model;

/**
 * @author nz
 */
abstract class Log implements LogInterface
{

    protected $source;
    protected $sourceId;
    protected $target;
    protected $targetId;
    protected $error = false;
    protected $notes;

    public function __construct($source = null, $target = null, $ex = null)
    {
        $this->setSource($source);
        $this->setTarget($target);

        /* $this->setSource($this->normalize($source)); */
        /* $this->setTarget($this->normalize($target)); */

        if ($ex) {
            $this->setError(true);
            $this->setNote('message', $ex->getMessage());
            $this->setNote('file', $ex->getFile());
            $this->setNote('line', $ex->getLine());
        }
    }

    private function normalize($arg)
    {
        return is_object($arg) ? sprintf('%s:%d', get_class($arg), $arg->getId())//
            : $arg;
    }

    /**
     * {@inheritdoc}
     */
    public function setSource($source)
    {
        if (is_object($source)) {
            $this->source = get_class($source);
            $this->setSourceId($source->getId());
        } else {

            $this->source = $source;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceId($id)
    {
        $this->sourceId = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTarget($target)
    {
        if (is_object($target)) {
            $this->target = get_class($target);
            $this->setTargetId(@$target->getId());
        } else {

            $this->target = $target;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetId($id)
    {
        $this->targetId = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * {@inheritdoc}
     */
    public function setNotes(array $notes = array())
    {
        $this->notes = $notes;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * {@inheritdoc}
     */
    public function setNote($name, $value)
    {
        $notes = $this->getNotes();

        $notes[$name] = $value;

        $this->setNotes($notes);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNote($name)
    {
        $notes = $this->getNotes();

        if (isset($notes[$name])) {
            return $notes[$name];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function removeNote($name)
    {
        $notes = $this->getNotes();

        if (isset($notes[$name])) {
            unset($notes[$name]);
        }

        return null;
    }

    public function __toString()
    {
        return sprintf('%s:%d:%s:%d', $this->source, $this->sourceId, !empty($this->target) ? $this->target : 'N/A', !empty($this->targetId) ? $this->targetId : 0);
        /* !empty($this->source) ? $this->source : 'n/a'; */
    }
}
