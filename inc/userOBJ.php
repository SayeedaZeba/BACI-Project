<?php
    class User{
        private $Username;
        private $Password;
        private $Email;
        private $Firstname;
        private $Lastname;
        private $Address;
        private $City;
        private $StateProvinceID;
        private $CountryID;
        private $PrivilegeID;
        private $SeekingMentorship;
        private $SeekingMenteeship;
        private $Mentor;

        private $Phones;
        private $Social_Media_Links;
        private $Mentees;
        private $Educations;

        public function __construct($Username, $Password, $Email, $Firstname, $Lastname, $Address, $City, $StateProvinceID, $CountryID, $PrivilegeID, $SeekingMentorship, $SeekingMenteeship, $Mentor) {
            $this->Username = $Username;
            $this->Password = $Password;
            $this->Email = $Email;
            $this->Firstname = $Firstname;
            $this->Lastname = $Lastname;
            $this->Address = $Address;
            $this->City = $City;
            $this->StateProvinceID = $StateProvinceID;
            $this->CountryID = $CountryID;
            $this->PrivilegeID = $PrivilegeID;
            $this->SeekingMentorship = $SeekingMentorship;
            $this->SeekingMenteeship = $SeekingMenteeship;
            $this->Mentor = $Mentor;
        }

        public function AddPhone($PhoneNumber, $NumberTypeID){
            if (!isset($this->Phones)){
                $newPhone = new Phone($PhoneNumber, $NumberTypeID);
                $this->Phones = Array($newPhone);
            }
            else{
                $newPhone = new Phone($PhoneNumber, $NumberTypeID);
                array_push($this->Phones, $newPhone);
            }
        }
        public function PopPhone(){
            try {
                return array_pop($this->Phones);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                return Null;
            }
        }

        public function AddMedia_Link($Link, $Type){
            if (!isset($this->Social_Media_Links)){
                $newMedia_Link = new Media_Link($Link, $Type);
                $this->Social_Media_Links = Array($newMedia_Link);
            }
            else{
                $newMedia_Links = new Media_Link($Link, $Type);
                array_push($this->Social_Media_Links, $newMedia_Links);
            }
        }
        public function PopMedia_Link(){
            try {
                return array_pop($this->Social_Media_Links);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";   /**/
                return Null;
            }
        }

        public function AddMenteeHistory($Mentee, $Start, $End){
            if (!isset($this->Mentees)){
                $newMenteeHistory = new MenteeHistory($Mentee, $Start, $End);
                $this->Mentees = Array($newMenteeHistory);
            }
            else{
                $newMenteeHistory = new MenteeHistory($Mentee, $Start, $End);
                array_push($this->Mentees, $newMenteeHistory);
            }
        }
        public function PopMenteeHistory(){
            try {
                return array_pop($this->Mentees);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                return Null;
            }
        }

        public function AddDegree($Type, $SchoolName, $Major){
            if (!isset($this->Educations)){
                $newDegree = new Degree($Type, $SchoolName, $Major);
                $this->Educations = Array($newDegree);
            }
            else{
                $newDegree = new Degree($Type, $SchoolName, $Major);
                array_push($this->Educations, $newDegree);
            }
        }
        public function PopDegree(){
            try {
                return array_pop($this->Educations);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                return Null;
            }
        }

        public function __get($property){
            if (property_exists($this, $property)) {
              return $this->$property;
            }
            else{
                echo $property." was not set yet";
            }
        }


        public function __set($property, $value) {
            if (property_exists($this, $property)) {
              $this->$property = $value;
            }
            else {
                echo "No such property named ".$property;
            }
        }

        public function getHashOfPassword(){
            if ($this->Password != Null) {
                return sha1($this->Password);
            }
            else {
                echo "Password is Null";
            }
        }

        public function getHashOfUsername(){
            if ($this->Username != Null) {
                return sha1($this->Username);
            }
            else {
                echo "UserUsername is Null";
            }
        }
    }

    class Degree{
        protected $Type;
        protected $SchoolName;
        protected $Major;

        public function __construct($Type, $SchoolName, $Major) {
            $this->Type = $Type;
            $this->SchoolName = $SchoolName;
            $this->Major = $Major;
        }
        public function __get($property){
            if (property_exists($this, $property)) {
              return $this->$property;
            }
            else{
                echo $property." was not set yet";
            }
        }

        public function __set($property, $value) {
            if (property_exists($this, $property)) {
              $this->$property = $value;
            }
            else {
                echo "No such property named ".$property;
            }
        }
    }

    class Phone{
        protected $PhoneNumber;
        protected $NumberTypeID;
        
        public function __construct($PhoneNumber, $NumberTypeID) {
            $this->PhoneNumber = $PhoneNumber;
            $this->NumberTypeID = $NumberTypeID;
        }
        public function __get($property){
            if (property_exists($this, $property)) {
              return $this->$property;
            }
            else{
                echo $property." was not set yet";
            }
        }

        public function __set($property, $value) {
            if (property_exists($this, $property)) {
              $this->$property = $value;
            }
            else {
                echo "No such property named ".$property;
            }
        }
    }
    
    class Media_Link{
        protected $Link;
        protected $Type;

        public function __construct($Link, $Type){
            $this->Link = $Link;
            $this->Type = $Type;
        }
        public function __get($property){
            if (property_exists($this, $property)) {
              return $this->$property;
            }
            else{
                echo $property." was not set yet";
            }
        }

        public function __set($property, $value) {
            if (property_exists($this, $property)) {
              $this->$property = $value;
            }
            else {
                echo "No such property named ".$property;
            }
        }
    }

    class MenteeHistory{
        protected $Mentee;
        protected $Start;
        protected $End;
        public function __construct($Mentee, $Start, $End){
            $this->Mentee = $Mentee;
            $this->Start = $Start;
            $this->End = $End;
        }
        public function __get($property){
            if (property_exists($this, $property)) {
              return $this->$property;
            }
            else{
                echo $property."was not set yet";
            }
        }

        public function __set($property, $value) {
            if (property_exists($this, $property)) {
              $this->$property = $value;
            }
            else {
                echo "No such property named ".$property;
            }
        }
    }


    /* Display how it work */

    /*How to insert information into User*/
    $user = new User("Duckknight","123","ABC@gmail.edu", "Duck", "Knight", "123 w 10 st", "Carmel", "IN", "USA", "1", "True", "False", 999);
    $user->AddPhone("312-781-6508", "01");
    $user->AddPhone("317-885-4821", "02");
    $user->AddMedia_Link("www.facebook.com", "facebook");
    $user->AddMedia_Link("www.indeed.com", "indeed");
    $user->AddMenteeHistory(998, "2015-07-05", "2017-09-01");
    $user->AddMenteeHistory(997, "2016-05-25", "2016-12-20");
    $user->AddDegree("Bachelor", "Indiana State University", "Biology");
    $user->AddDegree("Master", "Ball State University", "Biology");
    
     /*How to get the information from User*/
    $degree1 = $user->PopDegree();
    $degree2 = $user->PopDegree();
    echo nl2br("Degree1-> ".$degree1->Type." ".$degree1->SchoolName." ".$degree1->Major);
    echo nl2br("\r\nDegree2-> ".$degree2->Type." ".$degree2->SchoolName." ".$degree2->Major);

    $link1 = $user->PopMedia_Link();
    $link2 = $user->PopMedia_Link();
    echo nl2br("\r\nLink1-> ".$link1->Link." ".$link1->Type);
    echo nl2br("\r\nLink2-> ".$link2->Link." ".$link2->Type);

    $MenteeHistory1 = $user->PopMenteeHistory();
    $MenteeHistory2 = $user->PopMenteeHistory();
    echo nl2br("\r\nMenteeHistory1-> ".$MenteeHistory1->Mentee." ".$MenteeHistory1->Start." ".$MenteeHistory1->End);
    echo nl2br("\r\nMenteeHistory2-> ".$MenteeHistory2->Mentee." ".$MenteeHistory2->Start." ".$MenteeHistory2->End);

    $Phone1 = $user->PopPhone();
    $Phone2 = $user->PopPhone();
    echo nl2br("\r\nPhone1-> ".$Phone1->PhoneNumber." ".$Phone1->NumberTypeID);
    echo nl2br("\r\nPhone2-> ".$Phone2->PhoneNumber." ".$Phone2->NumberTypeID);

    echo nl2br("\r\nuser->"." ".$user->UserName." ".$user->Password." ".$user->Email." ".$user->Firstname." ".$user->LastName." ".$user->Address." ".$user->City." ".$user->StateprovienceID." ".$user->CountryID." ".$user->PrivilegeID." ".$user->SeekingMentorship." ".$user->SeekingMenteeship." ".$user->Mentor);

    /*these two methods can give the Hash value(as sha1 from) of the password and username*/
    echo nl2br("\r\n".$user->getHashOfPassword());
    echo nl2br("\r\n".$user->getHashOfUsername());

?>