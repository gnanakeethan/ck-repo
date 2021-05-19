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
		$url  .= ($parameters ? '?' . http_build_query($parameters) : '');
		//create a new client
		$redis = new Redis();
		$redis->connect('127.0.0.1', '6379');
		if (in_array($method, ['PUT', 'POST', 'PATCH', 'DELETE'])) {
			return $redis->del('request_cache_' . $url);
		}
		if (!in_array($method, ['PUT', 'POST', 'PATCH', 'DELETE']) && $redis->exists('request_cache_' . $url)) {
			// redis returns a string cached.
			return $redis->get('request_cache_' . $url);
		}
		//get new contents. no cache found / cache invalidated by the request type
		$cachableContent = file_get_contents($url, false, stream_context_create($opts));
		$redis->set('request_cache_' . $url, $cachableContent, 3600);

		//return the string cached
		return $cachableContent;
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

$simple1 = SimpleJsonRequest::get('https://jsonplaceholder.typicode.com/todos/1');
$simple2 = SimpleJsonRequest::get('https://jsonplaceholder.typicode.com/todos/2');
$simple3 = SimpleJsonRequest::get('https://jsonplaceholder.typicode.com/todos/1');
var_dump($simple1);
var_dump($simple2);
var_dump($simple3);
$redis = new Redis();
$redis->connect('127.0.0.1', '6379');
var_dump($redis->keys('request_cache_*'));