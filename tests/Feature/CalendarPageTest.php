<?php

test('calendar page returns a successful response', function () {
    $response = $this->get('/calendar');

    $response->assertStatus(200);
});
