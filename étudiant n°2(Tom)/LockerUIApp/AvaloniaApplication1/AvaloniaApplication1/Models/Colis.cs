using System.Text.Json.Serialization;

namespace AvaloniaLockerApp.Models
{
    public class Colis
    {
        [JsonPropertyName("idColis")]
        public int IdColis { get; set; }

        [JsonPropertyName("num_colis")]
        public string? NumColis { get; set; }

        [JsonPropertyName("Casier_idCasier")]
        public int? CasierIdCasier { get; set; }
    }
}