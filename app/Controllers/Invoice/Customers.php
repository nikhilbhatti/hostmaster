<?php

namespace App\Controllers\Invoice;

use App\Controllers\BaseController;
use App\Models\CustomerModel;

class Customers extends BaseController
{
    private $m;

    public function __construct()
    {
        $this->m = new CustomerModel();
    }

    public function index()
    {
        $s = $this->request->getGet('search');

        $q = $this->m->orderBy('display_name');

        if ($s) {
            $q = $q->groupStart()
                ->like('display_name', $s)
                ->orLike('email', $s)
                ->orLike('company_name', $s)
                ->groupEnd();
        }

        return view('invoice/customers/index', [
            'customers' => $q->findAll(),
            'search'    => $s
        ]);
    }

    public function create()
    {
        return view('invoice/customers/form', [
            'c'     => null,
            'title' => 'New Customer'
        ]);
    }

    public function store()
    {
        $d = $this->request->getPost();

        $d['shipping_same'] = isset($d['shipping_same']) ? 1 : 0;

        $this->m->insert($d);

        return redirect()
            ->to(base_url('invoice/customers'))
            ->with('success', 'Customer added successfully');
    }

    public function show($id)
    {
        $db = \Config\Database::connect();

        $invoices = $db->query(
            "SELECT * FROM invoices WHERE customer_id = ? ORDER BY id DESC",
            [$id]
        )->getResultArray();

        return view('invoice/customers/show', [
            'c'        => $this->m->find($id),
            'invoices' => $invoices
        ]);
    }

    public function edit($id)
    {
        return view('invoice/customers/form', [
            'c'     => $this->m->find($id),
            'title' => 'Edit Customer'
        ]);
    }

    public function update($id)
    {
        $d = $this->request->getPost();

        $d['shipping_same'] = isset($d['shipping_same']) ? 1 : 0;

        $this->m->update($id, $d);

        return redirect()
            ->to(base_url('invoice/customers'))
            ->with('success', 'Customer updated');
    }

    public function prefill()
    {
        $gstin = trim($this->request->getGet('gstin'));

        if (!$gstin) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'GSTIN is required']);
        }

        $apiKey = env('GST_API_KEY');
        $apiEndpoint = env('GST_API_ENDPOINT');

        if ($apiKey && $apiEndpoint) {
            return $this->prefillFromExternalApi($gstin, $apiEndpoint, $apiKey);
        }

        $customer = $this->m->where('gstin', $gstin)->first();

        if (!$customer) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['error' => 'Customer not found']);
        }

        return $this->response->setJSON($customer);
    }

    protected function prefillFromExternalApi(string $gstin, string $endpoint, string $apiKey)
    {
        $endpoint = rtrim($endpoint, '/');
        $url = $endpoint . '/' . urlencode($gstin);
        $apiHost = env('GST_API_HOST');
        if (!$apiHost) {
            $parsedUrl = parse_url($endpoint);
            $apiHost = $parsedUrl['host'] ?? null;
        }

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($apiHost) {
            $headers['x-rapidapi-host'] = $apiHost;
            $headers['x-rapidapi-key'] = $apiKey;
        } else {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
        }

        $verify = env('GST_API_VERIFY');
        if ($verify === null) {
            $verify = (ENVIRONMENT !== 'development');
        } else {
            $verify = filter_var($verify, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($verify === null) {
                $verify = true;
            }
        }

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->get($url, [
                'headers' => $headers,
                'timeout' => 15,
                'verify' => $verify,
            ]);

            if ($response->getStatusCode() !== 200) {
                $body = json_decode($response->getBody(), true);
                $message = null;

                if (is_array($body)) {
                    $message = $body['message'] ?? $body['error'] ?? null;
                    if (!$message && isset($body['status'])) {
                        $message = is_string($body['status']) ? $body['status'] : json_encode($body['status']);
                    }
                }

                if (!$message) {
                    $message = trim((string) $response->getBody());
                }

                return $this->response
                    ->setStatusCode($response->getStatusCode())
                    ->setJSON(['error' => 'External GST API returned an error: ' . $message]);
            }

            $rawBody = (string) $response->getBody();
            $data = json_decode($rawBody, true);

            if (!is_array($data) || empty($data)) {
                $bodyText = trim($rawBody);
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Invalid response from GST API: ' . $bodyText]);
            }

            $apiRaw = $data;
            $data = $data['data'] ?? $data['result'] ?? $data['response'] ?? $data;
            if (is_array($data) && isset($data[0]) && array_keys($data) === range(0, count($data) - 1)) {
                $data = $data[0];
            }

            $get = function(array $item, array $keys, $default = '') {
                foreach ($keys as $key) {
                    if (array_key_exists($key, $item) && $item[$key] !== null && $item[$key] !== '') {
                        return $item[$key];
                    }
                }
                return $default;
            };

            $address = $data['principalAddress']['address'] ?? $data['address'] ?? $data['registeredAddress'] ?? $data['billingAddress'] ?? $data['addresses'][0] ?? [];
            if (!is_array($address)) {
                $address = [];
            }

            $mapped = [
                'gstin' => $gstin,
                'company_name' => $get($data, ['company_name', 'businessName', 'legalName', 'tradeName', 'entityName', 'name', 'organisationName']),
                'display_name' => $get($data, ['tradeName', 'company_name', 'businessName', 'legalName', 'entityName', 'name', 'organisationName']),
                'pan' => $get($data, ['pan', 'PAN', 'panNumber', 'PANNumber']),
                'email' => $get($data, ['email', 'emailId', 'emailID', 'contactEmail']),
                'work_phone' => $get($data, ['phone', 'mobile', 'contactNumber', 'phoneNumber', 'mobileNumber']),
                'mobile' => $get($data, ['mobile', 'phone', 'contactNumber', 'mobileNumber', 'phoneNumber']),
                'b_attention' => $get($address, ['attention', 'contactPerson', 'attentionTo', 'attention_name', 'attn', 'buildingName', 'buildingNumber']),
                'b_country' => $get($address, ['country', 'countryCode', 'country_name']),
                'b_address1' => $get($address, ['buildingName', 'buildingNumber', 'street', 'address', 'address1', 'line1', 'address_line_1', 'address1_line']),
                'b_address2' => $get($address, ['location', 'locality', 'landMark', 'area', 'address2', 'line2', 'address_line_2', 'address2_line']),
                'b_city' => $get($address, ['district', 'city', 'town']),
                'b_state' => $get($address, ['stateCode', 'state', 'region', 'province']),
                'b_zip' => $get($address, ['pincode', 'postal_code', 'zip', 'postalCode']),
            ];

            if (ENVIRONMENT === 'development') {
                $mapped['__raw_api_response'] = $apiRaw;
            }

            return $this->response->setJSON($mapped);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'GST API request failed: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $this->m->delete($id);

        return redirect()
            ->to(base_url('invoice/customers'))
            ->with('success', 'Customer deleted');
    }
}