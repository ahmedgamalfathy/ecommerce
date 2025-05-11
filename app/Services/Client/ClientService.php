<?php
namespace App\Services\Client;
use App\Models\Client\Client;
use App\Filters\Client\FilterClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ClientService
{
    protected $clientService;
    protected $clientPhoneService;
    protected $clientEmailService;
    protected $clientAddressService;
   public function __construct(  ClientPhoneService $clientPhoneService, ClientEmailService $clientEmailService, ClientAddressService $clientAddressService)
    {
        $this->clientPhoneService = $clientPhoneService;
        $this->clientEmailService = $clientEmailService;
        $this->clientAddressService = $clientAddressService;
    }
    public function allClients()
    {
        $perPage = request()->get('pageSize', 10);
        $clients = QueryBuilder::for(Client::class)
        ->allowedFilters([
        AllowedFilter::custom('search', new FilterClient()),
        ])
        ->paginate($perPage); // Pagination applied here
        return $clients;
    }
    public function editClient(int $id)
    {
        return Client::with(['emails', 'phones', 'addresses'])->find($id);
    }
    public function createClient(array $data): Client
    {
        $client=Client::create([
            'name'=>$data['name'],
            'note'=>$data['note'],
        ]);
      if (isset($data['phones'])) {
        foreach ($data['phones'] as $phone) {
            $this->clientPhoneService->createClientPhone(['clientId'=>$client->id, ...$phone]);
        }
    }
    if (isset($data['emails'])) {
        foreach ($data['emails'] as $email) {
            $this->clientEmailService->createClientEmail(['clientId'=>$client->id, ...$email]);
        }
    }
    if (isset($data['addresses'])) {
        foreach ($data['addresses'] as $address) {
            $this->clientAddressService->createClientAddress(['clientId'=> $client->id, ...$address]);
        }
    }
      return $client;
    }
    public function updateClient(int $id, array $data )
    {
        $client = Client::find($id);
        $client->update([
            'name'=>$data['name'],
            'note'=>$data['note'],
        ]);
        return $client;
    }
    public function deleteClient(int $id):void
    {
        $client = Client::find($id);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $client->delete();
    }
}
