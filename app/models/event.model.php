<?php
/**
 * Модель, отвечающая за запись обработчиков событий и вызова этих обработчиков:
 */
class EventModel
{
	/**
	 * Массив лямбда-функций обработчиков:
	 */
	private $listeners = array();

	/**
	 * Подключение к рилплексору:
	 */
	private $connection;


	/**
	 * Вызов экземпляра класса:
	 */
	public static function getInstance()
	{
		static $instance;

		if (!is_object($instance))
			$instance = new EventModel();

		return $instance;
	}

	/**
	 * Подпись обработчика на событие:
	 */
	public function AddEventListener($event, $lambda)
	{
		$this -> listeners[$event][] = $lambda;
		return $this;
	}

	/**
	 * Очистка обработчиков события:
	 */
	public function RemoveEventListeners($event)
	{
		unset($this -> listeners[$event]);
	}

	/**
	 * Вызов цепочки обработчиков:
	 */
	public function Broadcast($event, $data = array())
	{
		if (!empty($this -> listeners))
		{
			foreach($this -> listeners as $event_name => $listeners)
			{
				if (fnmatch($event_name, $event))
				{
					foreach($listeners as $listener)
					{
						$listener($data, $event);
					}
				}
			}
		}
		return true;
	}

	/**
	 * Сообщение о событии на клиенты:
	 */
	public function ClientBroadcast($channel, $event, $data = null, $ids = null)
	{
		$payload = [
			'channel' => $channel,
			'event' => $event,
			'token' => SIO_TOKEN
		];
		if ($data) {
			$payload['data'] = $data;
		}
		if ($ids) {
			$payload['ids'] = $ids;
		}
		$data_json = json_encode($payload);
		
		$curl_session = curl_init(SIO_HOST . ':' . SIO_PORT . '/broadcast/');
		curl_setopt($curl_session, CURLOPT_PROXY, "");
		curl_setopt($curl_session, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_session, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_json)
		]);
		curl_exec($curl_session);
		if (curl_errno($curl_session)) {
			die('cURL error: ' . curl_error($curl_session));
		}
		return $this;
	}
}
