using System;
using System.Net.Http;
using System.Text.Json;
using System.Threading.Tasks;
using LockerRaspberry.Models;

namespace LockerRaspberry.Services
{
    public class ApiService
    {
        private readonly HttpClient _httpClient;

        private readonly string _baseUrl = "http://172.18.199.9/";

        public ApiService()
        {
            _httpClient = new HttpClient();
        }

        public async Task<bool> TesterConnexionApiAsync()
        {
            try
            {
                string url = _baseUrl + "colis.php";
                HttpResponseMessage response = await _httpClient.GetAsync(url);

                return response.IsSuccessStatusCode;
            }
            catch
            {
                return false;
            }
        }

        public async Task<Colis?> RechercherColisAsync(string numeroColis)
        {
            try
            {
                string url = _baseUrl + "colis.php?track=" + numeroColis;

                string json = await _httpClient.GetStringAsync(url);

                if (string.IsNullOrWhiteSpace(json))
                    return null;

                Colis? colis = JsonSerializer.Deserialize<Colis>(json);

                if (colis == null)
                    return null;

                if (colis.Success == false)
                    return null;

                return colis;
            }
            catch
            {
                return null;
            }
        }
    }
}