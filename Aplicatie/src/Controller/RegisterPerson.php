<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Cache\Cache;

class RegisterPersonController extends AppController
{

        public function initialize()
        {
                parent::initialize();
                $this->loadComponent('RequestHandler');

        }

        public function addParticipant($programmeID)
        {
        $participant = $this->Participants->newEntity();
        if ($this->request->is('post')) { 
        $participant = $this->Programmes->patchEntity($participant, $this->request->data);
        if ($this->Participant->save($participant)) {
                $message = 'Participant saved';
                }else{
                $message = 'Error1';
                }
        }
        $this->set([
        'message' => $message,
        'programme' => $participant,
        '_serialize' => ['message', 'participant']
        ]);
        }
        }
?>