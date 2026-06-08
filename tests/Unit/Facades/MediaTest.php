<?php

use Actengage\Media\Facades\Media as MediaFacade;
use Actengage\Media\Media;

it('resolves the media model from the facade', function (): void {
    expect(MediaFacade::getFacadeRoot())->toBeInstanceOf(Media::class);
});
