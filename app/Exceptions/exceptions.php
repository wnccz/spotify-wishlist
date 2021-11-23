<?php

namespace Wnc;

class SpotifyServiceException extends \Nette\InvalidStateException
{
}

class NoActiveDeviceException extends SpotifyServiceException 
{
}
