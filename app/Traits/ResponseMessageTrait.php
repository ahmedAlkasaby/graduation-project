<?php
namespace App\Traits;
trait ResponseMessageTrait {

    public function getMessageError($code = null){
        $message = NULL;
        switch ($code){
            case 'password_old':
                $message = __("password does not match the database password");
            break;
            case 'image':
                $message =  __('Upload Image Fail');
                break;
            case 'address':
                $message = __('Update Address Fail');
                break;
            case 'locale':
                $message = __('Update Language Fail');
                break;
            case 'address_not_found':
                $message = __('Address not found');
                break;
            case 'address_delete':
                $message = __('Delete Default Address Fail');
                break;
            case 'phone':
                $message = __("We can't find a user with that phone.");
                break;
            case 'email':
                $message = __("We can't find a user with that e-mail address.");
                break;
            case 'token':
                $message =  __('This password reset token is invalid.');
                break;
            case 'code':
                $message =  __('Code not found');
                break;
            case 'code_send':
                $message =  __('Code send before');
                break;
            case 'user':
                $message =  __('User not found');
                break;
            case 'project':
                $message =  __('Project not found');
                break;
            case 'product':
                $message =  __('Product not found');
                break;
            case 'category':
                $message =  __('Category not found');
                break;
            case 'not_found':
                $message =  __('Not found');
                break;
            case 'delete':
                $message =  __('Delete');
                break;
            case 'notifi_not_found':
                $message =  __('Notification Not found');
                break;
            case 'notifi_delete':
                $message =  __('Notification Delete');
                break;
            case 'unauthorise':
                $message =  __('Unauthorise');
                break;
            case 'device':
                $message =  __('Login From Other Device');
                break;
            case 'time':
                $message =  __("please don't make it again in another time i will block your account");
                break;
            case 'code_expire':
                $message =  __('Code Expire');
                break;
            case 'code_not_valid':
                $message =  __('Code Not Valid');
                break;
        }
        return $message;
    }

    public function getMessageSuccess($code = null){
        $message = NULL;
        switch ($code){
            case 'password':
                $message = __('Password update successfully');
                break;
            case 'profile':
                $message = __('Profile update successfully');
                break;
            case 'locale':
                $message = __('Language update successfully');
                break;
            case 'address':
                $message = __('Address update successfully');
                break;
            case 'image_update':
                $message = __('Image update successfully');
                break;
            case 'image_delete':
                $message = __('Delete Image');
                break;
            case 'action':
                $message = __('Action update successfully');
                break;
            case 'available':
                $message = __('Available update successfully');
                break;
            case 'location':
                $message = __('Location update successfully');
                break;
            case 'image':
                $message =  __('Image upload successfully');
                break;
            case 'product':
                $message = __("Product send successfully");
                break;
            case 'contact':
                $message = __("Contact Us send successfully");
                break;
            case 'comment':
                $message = __("Comment send successfully");
                break;
            case 'success':
                $message =  __('Send Successfully');
                break;
            case 'logout':
                $message =  __('Successfully logged out');
                break;
        }
        return $message;
    }

}