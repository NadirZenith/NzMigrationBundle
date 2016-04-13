<?php

namespace Nz\MigrationBundle\Model;

/**
 * @author nz
 */
interface LogInterface
{

    /**
     * Returns the id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Set source.
     *
     * @param string $source
     */
    public function setSource($source);

    /**
     * Get name.
     *
     * @return string
     */
    public function getSource();

    /**
     * Set Target.
     *
     * @param string $target
     */
    public function setTarget($target);

    /**
     * Get Target.
     *
     * @return string
     */
    public function getTarget();

    /**
     * Get error.
     *
     * @return bool $error
     */
    public function getError();

    /**
     * If occurs any error when processing url
     *
     * Set error
     *
     * @param bool $error
     */
    public function setError($error);

    /**
     * Set notes.
     *
     * @param string $notes
     */
    public function setNotes(array $notes = array());

    /**
     * Get notes.
     *
     * @return array
     */
    public function getNotes();

    /**
     * Add note.
     *
     * @param string $notes
     */
    public function setNote($name, $value);

    /**
     * Get note.
     *
     * @param string $name note name
     * @return string | null;
     */
    public function getNote($name);

    /**
     * remove note.
     *
     * @param string $name note name
     */
    public function removeNote($name);
}
