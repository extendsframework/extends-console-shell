<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Task;

interface TaskInterface
{
    /**
     * Execute task.
     *
     * @param array $data
     * @return void
     * @throws TaskException
     */
    public function execute(array $data): void;
}
