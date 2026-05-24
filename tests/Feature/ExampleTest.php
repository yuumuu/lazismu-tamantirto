<?php

test('returns a successful response', function () {
    $response = $this->get(route('guest.home'));

    $response->assertStatus(200);
});
