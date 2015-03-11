<?php

Route::filter('allowDifferentToken', function () {
    Auth::allowDifferentToken(true);
});
