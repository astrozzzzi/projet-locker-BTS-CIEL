using System;
using System.Net.Http;
using System.Text.Json;
using System.Threading.Tasks;
using LockerRaspberry.Models;

namespace LockerRaspberry.Services
{
    public class ApiService
    {
        private readonly HttpClient httpClient;
        private readonly string baseUrl = "http://172.18.199.9/API_locker/";

        public ApiService()
        {
            httpClient = new HttpClient();
        }

        public async Task<string> TesterApiAsync()
        {
            try
            {
                string url = baseUrl + "colis.php";

                HttpResponseMessage response = await httpClient.GetAsync(url);
                string contenu = await response.Content.ReadAsStringAsync();

                if (response.IsSuccessStatusCode)
                {
                    return "API accessible : " + contenu;
                }

                return "Erreur API : " + response.StatusCode;
            }
            catch (Exception ex)
            {
                return "Erreur connexion API : " + ex.Message;
            }
        }

        public async Task<Colis?> RechercherColisAsync(string numeroColis)
        {
            try
            {
                string url = baseUrl + "colis.php?track=" + numeroColis;

                string json = await httpClient.GetStringAsync(url);

                if (string.IsNullOrWhiteSpace(json))
                    return null;

                Colis? colis = JsonSerializer.Deserialize<Colis>(json);

                return colis;
            }
            catch
            {
                return null;
            }
        }
    }
}