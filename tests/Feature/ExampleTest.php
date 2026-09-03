<?php

it('renders the executive summary landing page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('SIPAS-Hub');
    $response->assertSee('Executive Summary', false);
});

it('renders executive summary via direct route', function () {
    $response = $this->get(route('executive.summary'));

    $response->assertStatus(200);
    $response->assertSee('Syahriyah');
});
