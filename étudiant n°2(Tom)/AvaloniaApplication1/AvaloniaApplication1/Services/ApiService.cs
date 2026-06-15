using System;
using System.Net.Http;
using System.Text.Json;
using System.Threading.Tasks;
using AvaloniaLockerApp.Models;

namespace AvaloniaLockerApp.Services
{
    public class ApiService
    {
        private readonly HttpClient httpClient;

        private readonly string baseUrl = "http://172.18.199.9/API_locker/";

        public ApiService()
        {
            httpClient = new HttpClient();
        }

        public async Task<Colis?> RechercherColisAsync(string codeColis)
        {
            try
            {
                string url = baseUrl + "colis.php?code_colis=" + codeColis;

                string json = await httpClient.GetStringAsync(url);

                if (string.IsNullOrWhiteSpace(json))
                {
                    return null;
                }

                JsonSerializerOptions options = new JsonSerializerOptions
                {
                    PropertyNameCaseInsensitive = true
                };

                Colis? colis = JsonSerializer.Deserialize<Colis>(json, options);

                if (colis == null)
                {
                    return null;
                }

                if (colis.IdColis == 0 || string.IsNullOrWhiteSpace(colis.CodeColis))
                {
                    return null;
                }

                return colis;
            }
            catch
            {
                return null;
            }
        }
    }
}