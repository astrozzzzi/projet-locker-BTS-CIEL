using System.Net.Http;
using System.Text;
using System.Text.Json;
using System.Threading.Tasks;

namespace LockerRaspberry.Services
{
    public class ApiService
    {
        private readonly HttpClient _httpClient = new HttpClient();

        private readonly string _baseUrl = "http://172.18.199.9/";

        public async Task<string> LoginClientAsync(string email, string password)
        {
            var data = new
            {
                action = "login",
                email = email,
                password = password
            };

            return await PostJsonAsync("user.php", data);
        }

        public async Task<string> LoginLivreurAsync(string email, string password)
        {
            var data = new
            {
                action = "loginLivreur",
                email = email,
                password = password
            };

            return await PostJsonAsync("user.php", data);
        }

        public async Task<string> TrackColisAsync(string numeroColis)
        {
            return await _httpClient.GetStringAsync(
                _baseUrl + "colis.php?track=" + numeroColis
            );
        }

        private async Task<string> PostJsonAsync(string endpoint, object data)
        {
            string json = JsonSerializer.Serialize(data);

            var content = new StringContent(
                json,
                Encoding.UTF8,
                "application/json"
            );

            var response = await _httpClient.PostAsync(_baseUrl + endpoint, content);

            return await response.Content.ReadAsStringAsync();
        }
    }
}