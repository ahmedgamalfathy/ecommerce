<?php
namespace App\Services\Client;

use App\Enums\IsMain;
use App\Models\Client\ClientPhone;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ClientPhoneService
{

   public function allClientPhones(int $clientId)
   {
     return ClientPhone::where('client_id',$clientId)->get();


   }
   public function editClientPhone(int $id)
   {
      return ClientPhone::find($id);
   }
    public function createClientPhone(array $data)
    {
        return  ClientPhone::create([
            'client_id' => $data['clientId'],
            'phone' => $data['phone'],
            'is_main' => IsMain::from($data['isMain'])->value,
            'country_code' => $data['countryCode'] ?? null,
        ]);

    }

    public function updateClientPhone(int $id, array $data)
    {
        $client = ClientPhone::find($id);
        $client->update([
            'phone' => $data['phone'],
            'is_main' =>  IsMain::from($data['isMain'])->value,
            'country_code' => $data['countryCode'] ?? null,
        ]);
        return $client;
    }

    public function deleteClientPhone(int $id)
    {
        $clientPhone = ClientPhone::find($id);
        if(!$clientPhone){
            throw new ModelNotFoundException();
        }
        $clientPhone->delete();
    }

}
