<?php
class contactform extends CI_Controller
{
    public function __construct() //construktor
    {
        parent::__construct();
        $this->load->helper(array('form','url'));
        $this->load->library(array('session', 'form_validation', 'email','captcha'));
        $this->load->model('MailList_model');
        
    }

    function index()
    {
        //setvame validation rules
        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|callback_alpha_space_only');
        $this->form_validation->set_rules('email', 'Emaid ID', 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required|xss_clean');
        $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');
        $this->form_validation->set_rules('captcha', 'CAPTCHA', 'trim|required|callback_captcha|xss_clean');
        

        //puskame valiadaciq na form input
        if ($this->form_validation->run() == FALSE)
        {   
            //validation fails and captcha
            $data['captcha'] = $this->captcha->main();
            $this->session->set_userdata('captcha_info', $data['captcha']);
            $this->load->view('contact_form_view',$data);
        }
        else
        {
            //get the form data
            $name = $this->input->post('name');
            $from_email = $this->input->post('email');
            $subject = $this->input->post('subject');
            $message = $this->input->post('message');
            
            $postdata= array(
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'subject' => $this->input->post('subject'),
                'message' => $this->input->post('message')
            );
            $this->MailList_model->insert($postdata);

            //set to_email id to which you want to receive mails
            $to_email = 'asen.tanev.work@gmail.com';

            //configure email settings
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'in-v3.mailjet.com';
            $config['smtp_port'] = '587';
            $config['smtp_user'] = 'd20c122cd57132521cedab4d4024201f';
            $config['smtp_pass'] = '693e60f86abf916b6db7d0c56ab61a03';
            $config['mailtype'] = 'html';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['newline'] = "\r\n"; //use double quotes
            //$this->load->library('email', $config);
            $this->email->initialize($config);                        

            //send mail
            $this->email->from($from_email, $name);
            $this->email->to($to_email);
            $this->email->subject($subject);
            $this->email->message($message);
            if ($this->email->send())
            {
                // mail sent
                $this->session->set_flashdata('msg','<div class="alert alert-success text-center">Your mail has been sent successfully!</div>');
                redirect('contactform/index');
            }
            else
            {
                //error
                $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">There is error in sending mail! Please try again later</div>');
                redirect('contactform/index');
            }
        }
    }
    
    //custom validation function to accept only alphabets and space input
    function alpha_space_only($str)
    {
        if (!preg_match("/^[a-zA-Z ]+$/",$str))
        {
            $this->form_validation->set_message('alpha_space_only', 'The %s field must contain only alphabets and space');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }


    return true;
}
}
?>
