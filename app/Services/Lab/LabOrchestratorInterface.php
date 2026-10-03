<?php

declare(strict_types=1);

namespace App\Services\Lab;

/**
 * Controlled Lab Orchestrator contract.
 * PHP never accepts docker run strings from users — only approved templates.
 */
interface LabOrchestratorInterface
{
    /**
     * @param array{
     *   instance_id:int,
     *   instance_identifier:string,
     *   lab_id:int,
     *   template_slug:string,
     *   resources:array{cpu:float,memory_mb:int,disk_mb:int},
     *   allow_internet:bool,
     *   secrets:array<string,string>
     * } $request
     * @return array{orchestrator_ref:string,provision_state:string}
     */
    public function provision(array $request): array;

    /**
     * @return array{provision_state:string,status:string,detail:?string}
     */
    public function status(string $orchestratorRef): array;

    public function stop(string $orchestratorRef): void;

    public function destroy(string $orchestratorRef): void;

    public function reset(string $orchestratorRef): void;
}
