<?php

class SimpleJsonRequest
{
	private static function makeRequest(string $method, string $url, array $parameters = null, array $data = null)
	{
		$opts = [
			'http' => [
				'method'  => $method,
				'header'  => 'Content-type: application/json',
				'content' => $data ? json_encode($data) : null,
			],
		];
		$url .= ($parameters ? '?' . http_build_query($parameters) : '');

		return file_get_contents($url, false, stream_context_create($opts));
	}

	public static function get(string $url, array $parameters = null)
	{
		return json_decode(self::makeRequest('GET', $url, $parameters));
	}

	public static function post(string $url, array $parameters = null, array $data)
	{
		return json_decode(self::makeRequest('POST', $url, $parameters, $data));
	}

	public static function put(string $url, array $parameters = null, array $data)
	{
		return json_decode(self::makeRequest('PUT', $url, $parameters, $data));
	}

	public static function patch(string $url, array $parameters = null, array $data)
	{
		return json_decode(self::makeRequest('PATCH', $url, $parameters, $data));
	}

	public static function delete(string $url, array $parameters = null, array $data = null)
	{
		return json_decode(self::makeRequest('DELETE', $url, $parameters, $data));
	}
}