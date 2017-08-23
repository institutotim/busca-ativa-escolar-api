<?php
/**
 * busca-ativa-escolar-api
 * StateSignupApproved.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2017
 *
 * @author Aryel Tupinambá <aryel.tupinamba@lqdi.net>
 *
 * Created at: 22/08/2017, 19:43
 */

namespace BuscaAtivaEscolar\Mailables;


use BuscaAtivaEscolar\StateSignup;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;

class StateSignupApproved extends Mailable {

	public $signup;

	public function __construct(StateSignup $signup) {
		$this->signup = $signup;
	}

	public function build() {

		$message = (new MailMessage())
			->subject("Sua adesão foi aprovada!")
			->greeting('Olá!')
			->line("A adesão como gestor estadual de {$this->signup->uf} à Busca Ativa Escolar foi aprovada.")
			->line('Clique no botão abaixo para acessar.')
			->success()
			->action('Acessar', url('/'));

		$this->from(env('MAIL_USERNAME'), 'Busca Ativa Escolar');
		$this->subject("[Busca Ativa Escolar] Sua adesão foi aprovada!");

		return $this->view('vendor.notifications.email', $message->toArray());

	}

}