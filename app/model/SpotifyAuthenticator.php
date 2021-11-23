<?php
namespace Wnc\Wishlist;

use Nette;
use Nette\Security\IIdentity;
use Nette\Security\SimpleIdentity;

class SpotifyAuthenticator implements Nette\Security\Authenticator
{
	/** @var \Wnc\Wishlist\spotifyService */
	private $spotify;
	public function __construct(
		Nette\Database\Explorer $database,
		Nette\Security\Passwords $passwords,
		\Wnc\Wishlist\spotifyService $spotify
	) {
		$this->database = $database;
		$this->passwords = $passwords;
		$this->spotify = $spotify;
	}

	public function authenticate(string $username, string $password = null): IIdentity
	{
		$token = $username;
		$data = $this->spotify->getUserProfile($token);

		$res = $this->database->table("user")->wherePrimary($data->id);
		if (!$res->count()) {
			$this->database->table("user")->insert(
				[
					'id' => $data->id,
					'display_name' => $data->display_name,
					'email' => $data->email,
					'image' => $data->images[0]->url,
					'uri' => $data->uri
				]);
		}

		
		return new SimpleIdentity($data->id, 'user', [
			'display_name' => $data->display_name,
			'email' => $data->email,
			'image' => $data->images[0]->url,
			'uri' => $data->uri,
			'token' => $token
		]);
	}
}