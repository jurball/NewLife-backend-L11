<?php

it('returns a not found response', function () {
    $response = $this->get('/');

    $response->assertStatus(404);
});
