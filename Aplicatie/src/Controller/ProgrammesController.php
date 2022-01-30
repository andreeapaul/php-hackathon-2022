<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Cache\Cache;

class ProgrammesController extends AppController
{

        public function initialize()
        {
                parent::initialize();
                $this->loadComponent('RequestHandler');
                $this->loadModel('Bookings');
                $this->loadModel('Admins');

        }

        public function index()
        {
                $programmes = $this->Programmes->find('all');
                $this->set([
                'programmes'=>$programmes,
                '_serialize'=>['programmes']
                ]);

        }

        public function view($id)
        {
                $programme = $this->Programmes->get($id);
                $this->set([
                'programme'=>$programme,
                '_serialize'=>['programme']
                ]);
        }

        public function add()
        {
                $programme = $this->Programmes->newEntity(); 
                $token = $this->request->header('admin');
                $isAdmin = json_decode($this->isAdmin($token));
                if(!empty($isAdmin)){
                    $roomID = $this->request->data['roomID'];
                    $query = $this->Programmes->find('all')
                    ->select(['start', 'end', 'roomID'])
                    ->where(['roomID = ' => $roomID]);
                    
                    $start = new  \DateTime($this->request->data['start']);
                    $end = $this->request->data['end'];
                    $programmesInTheRoom = $query->toArray();
                    
                    $ok = array();
                    foreach($programmesInTheRoom as $k=>$programme){
                        if(($start >= $programme['start']) && ($start <= $programme['end'])){
                            $ok[$k] = 0;
                        }else{
                            $ok[$k] = 1;
                        }
                    }
                      
                    if(in_array(0, $ok)){
                        $message = "This room is not available for your chosen time slot.";
                    }else{
                        $programme = $this->Programmes->patchEntity($programme, $this->request->data);
                        $this->Programmes->save($programme);
                        $message = "Programme successfully added!";
                    }
                }else{
                    $message = "Only admins can add programmes!";
                }
            
        
        $this->set([
        'message' => $message,
        'programme' => $programme,
        '_serialize' => ['message', 'programme']
        ]);
        }


        public function delete($id)
        {
        $this->request->allowMethod(['post', 'delete']);
        $token = $this->request->header('admin');
        $isAdmin = json_decode($this->isAdmin($token));
        if(!empty($isAdmin)){
            $programme = $this->Programmes->get($id);
             if ($this->Programmes->delete($programme)) {    
            $message = 'Programme deleted';
            }else{
            $message = 'Error';
            }
        
        }else{
            $message = "Only admins can delete programmes.";
        }
        $this->set([
        'message'=>$message,
        '_serialize'=>['message']
        ]);
        }
        

        public function bookProgramme()
        {
            if ($this->request->is('post')) { 
            $booking = $this->Bookings->newEntity();
            $cnp = $this->request->data['CNP'];
            $programmeID = $this->request->data['programmeID'];
            $query = $this->Programmes->find('all', array(
                'join' => array(
                    array(
                        'table' => 'bookings',
                        'alias' => 'b',
                        'type' => 'INNER',
                        'conditions' => array(
                            'b.programmeID = Programmes.id'
                        )
                    )
                ),
                'conditions' => array(
                    'b.CNP' => $cnp
                ),
                'fields' => array('Programmes.start', 'Programmes.end')
            ));
    
            $found_programmes = $this->Programmes->find()
            ->select(['start', 'end', 'max_participants', 'no_registered_persons'])
            ->where(['id =' => $programmeID ]);
    
            $orar = $found_programmes->toArray();
            $start = $orar[0]['start'];
            $end = $orar[0]['end'];
           
            $data = $query->toArray();
            $ok = array();
            $max_participants = array();
            foreach($data as $k=>$value){
                if(($start >= $value['start']) && ($start <= $value['end'])){
                   $ok[$k] = 0;
                }else{
                    $ok[$k] = 1; 
                }
            
            }
            if(in_array(0, $ok)){
                $message = "You cannot register for two programs that take place at the same time!";
            }
            else if($orar[0]['no_registered_persons'] == $orar[0]['max_participants']){
                    $message = "Sorry, the programme has already reached the maximum number of participants";
                }else{
                $saveData = [
                    'CNP' => $cnp,
                    'programmeID' => $programmeID
                ];
                $validare = json_decode($this->validareCNP($cnp));
                if(!empty($validare)){
                    $message = $validare;
                }else{
                $booking = $this->Bookings->patchEntity($booking, $saveData);
                $this->Bookings->save($booking);
                $programme = $this->Programmes->get($programmeID);
                $programme->no_registered_persons = $programme->no_registered_persons+1;
                $this->Programmes->save($programme);
                $message = "Registered participant!";
            }
        }
        }
        $this->set([
            'message' => $message,
            'programmeID'=> $programmeID,
            'cnp' => $cnp,
            'booking'=> $booking,
            '_serialize' => ['message', 'programmeID', 'cnp', 'booking']]);
    }
    

        function validareCNP($cnp){
            $erori = array();
            if(strlen($cnp) != 13) {
            $erori[] = "CNP-ul trebuie sa fie format din 13 cifre!";
            }
            for($i=0 ; $i<13 ; $i++) {
                if(!is_numeric($cnp[$i])) {
                $erori[] = "Toate caracterele din CNP trebuie sa fie numerice!";
                }
            }
            $c['cnp'] = $cnp;
            $c['sex'] = $c['cnp']{0}; // 1, 2, 5, 6
            $c['an'] = $c['cnp']{1}.$c['cnp']{2};   
            $c['luna'] = $c['cnp']{3}.$c['cnp']{4}; // intre 1 si 12
            $c['zi'] = $c['cnp']{5}.$c['cnp']{6};   //intre 1 si 31
            $c['judet'] = $c['cnp']{7}.$c['cnp']{8};

            if($c['sex'] != 1 && $c['sex'] != 2 && $c['sex'] != 5 && $c['sex'] != 6){
            $erori[] = "Prima cifra din CNP poate fi doar 1, 2, 5, 6";
            }
            if((int)$c['luna'] > 12 || (int)$c['luna'] == 0 ){
                $erori[] = "Luna incorecta.";
            }
            if((int)$c['zi'] > 31 || (int)$c['zi'] == 0)
                {
                    $erori[] = "Zi incorecta.";
                }
            if((int)$c['judet'] > 52 || (int)$c['judet'] == 0)
                {
                $erori[] = "Codul judetului eronat.";
                }
            
           
           $this->response->body(json_encode($erori));
           return $this->response;
        }

        public function isAdmin($token){
            $query = $this->Admins->find('all' , array(
                'conditions' => array(
                    'Admins.random_token' => $token)
            ));

            $admin = $query->toArray();
            $this->response->body(json_encode($admin));
            return $this->response;
        }
}
?>