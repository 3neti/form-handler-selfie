<?php

use Illuminate\Http\Request;
use LBHurtado\FormFlowManager\Data\FormFlowStepData;
use LBHurtado\FormFlowManager\Services\FormFlowPresentationResolver;
use LBHurtado\FormHandlerSelfie\SelfieHandler;

it('keeps the resolved presentation identical in live and inert preview responses', function () {
    $policy = app(FormFlowPresentationResolver::class)->resolve(['brand' => ['name' => 'Synthetic Brand']]);
    $step = FormFlowStepData::from(['handler' => 'selfie', 'config' => ['presentation' => $policy]]);
    $handler = app(SelfieHandler::class);
    $preview = $handler->preview($step, ['flow_id' => 'synthetic', 'step_index' => 0]);
    $request = Request::create('/synthetic', 'GET', server: ['HTTP_X_INERTIA' => 'true']);
    $live = $handler->render($step, ['flow_id' => 'synthetic', 'step_index' => 0])->toResponse($request)->getData(true);
    expect($preview['props']['presentation'])->toBe($policy)
        ->and($live['props']['presentation'])->toBe($policy)
        ->and($preview['props']['preview_mode'])->toBeTrue();
});

it('registers the selfie screen and resolves its bounded presentation overrides', function () {
    $policy = app(FormFlowPresentationResolver::class)->resolve([
        'screens' => ['selfie' => ['tokens' => ['control_size' => 'large'], 'actions' => ['roles' => ['retry' => ['label' => 'Try again']]]]],
    ], [], 'selfie');
    expect($policy['tokens']['control_size'])->toBe('large')
        ->and($policy['actions']['roles']['retry']['label'])->toBe('Try again');
});
