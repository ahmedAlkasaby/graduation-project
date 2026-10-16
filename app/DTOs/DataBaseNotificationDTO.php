<?php
namespace App\DTOs;
class DataBaseNotificationDTO
{
    public function __construct(
        public array $title,
        public array $message,
        public string $type='admin',
        public ?int $model_id=null,
        public ?string $model=null,
        public ?string $status=null,
        public ?string  $image=null,  
    ) {}

      public function toArray(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'model' => $this->model,
            'model_id' => $this->model_id,
            'status' => $this->status,
            'image' => $this->image,
        ];
    }

   
    
}