using System;
using System.Net.Http;
using System.Text.Json;
using System.Threading.Tasks;

namespace LockerControllerApp.Services
{
    public class ApiService
    {
        private readonly HttpClient _httpClient;
        private readonly string _baseUrl = "http://172.18.199.9/colis.php";

        public ApiService()
        {
            _httpClient = new HttpClient();
        }

        public async Task<bool> VerifierColisAsync(string idColis)
        {
            try
            {
                string url = $"{_baseUrl}?id={idColis}";
                string json = await _httpClient.GetStringAsync(url);

                return !string.IsNullOrWhiteSpace(json) && json != "null";
            }
            catch (Exception)
            {
                return false;
            }
        }

        public async Task<string> LireColisAsync(string idColis)
        {
            try
            {
                string url = $"{_baseUrl}?id={idColis}";
                return await _httpClient.GetStringAsync(url);
            }
            catch (Exception ex)
            {
                return $"Erreur API : {ex.Message}";
            }
        }
    }
}