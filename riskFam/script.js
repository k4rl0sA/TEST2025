// Initial data
let socioeconomic = 60;
let familyStructure = 45;
let healthConditions = 70;
let socialVulnerability = 50;
let accessToHealth = 30;
let livingEnvironment = 65;
let demographics = 40;
let totalRisk = 55;
let riskLevel = "Medium Risk";

// Get DOM elements
const generateBtn = document.getElementById('generateBtn');
const riskAlert = document.getElementById('riskAlert');
const riskLevelText = document.getElementById('riskLevelText');
const totalRiskText = document.getElementById('totalRiskText');
const totalText = document.getElementById('totalText');
const totalValue = document.getElementById('totalValue');
const totalBar = document.getElementById('totalBar');

// Risk factor elements
const factors = [
  {
    name: 'socioeconomic',
    weight: 0.20,
    value: socioeconomic,
    textEl: document.getElementById('socioeconomicText'),
    valueEl: document.getElementById('socioeconomicValue'),
    barEl: document.getElementById('socioeconomicBar')
  },
  {
    name: 'familyStructure',
    weight: 0.15,
    value: familyStructure,
    textEl: document.getElementById('familyStructureText'),
    valueEl: document.getElementById('familyStructureValue'),
    barEl: document.getElementById('familyStructureBar')
  },
  {
    name: 'healthConditions',
    weight: 0.20,
    value: healthConditions,
    textEl: document.getElementById('healthConditionsText'),
    valueEl: document.getElementById('healthConditionsValue'),
    barEl: document.getElementById('healthConditionsBar')
  },
  {
    name: 'socialVulnerability',
    weight: 0.15,
    value: socialVulnerability,
    textEl: document.getElementById('socialVulnerabilityText'),
    valueEl: document.getElementById('socialVulnerabilityValue'),
    barEl: document.getElementById('socialVulnerabilityBar')
  },
  {
    name: 'accessToHealth',
    weight: 0.10,
    value: accessToHealth,
    textEl: document.getElementById('accessToHealthText'),
    valueEl: document.getElementById('accessToHealthValue'),
    barEl: document.getElementById('accessToHealthBar')
  },
  {
    name: 'livingEnvironment',
    weight: 0.10,
    value: livingEnvironment,
    textEl: document.getElementById('livingEnvironmentText'),
    valueEl: document.getElementById('livingEnvironmentValue'),
    barEl: document.getElementById('livingEnvironmentBar')
  },
  {
    name: 'demographics',
    weight: 0.10,
    value: demographics,
    textEl: document.getElementById('demographicsText'),
    valueEl: document.getElementById('demographicsValue'),
    barEl: document.getElementById('demographicsBar')
  }
];

// Generate random data
function generateRandomData() {
  factors.forEach(factor => {
    factor.value = Math.floor(Math.random() * 100);
    updateFactorUI(factor);
  });
  
  calculateTotalRisk();
}

// Calculate total risk
function calculateTotalRisk() {
  totalRisk = factors.reduce((sum, factor) => sum + (factor.value * factor.weight), 0);
  
  if (totalRisk >= 71) {
    riskLevel = "High Risk";
    riskAlert.className = "alert high";
    totalBar.className = "progress-bar-fill high";
  } else if (totalRisk >= 41) {
    riskLevel = "Medium Risk";
    riskAlert.className = "alert medium";
    totalBar.className = "progress-bar-fill medium";
  } else {
    riskLevel = "Low Risk";
    riskAlert.className = "alert low";
    totalBar.className = "progress-bar-fill low";
  }
  
  riskLevelText.textContent = riskLevel;
  totalRiskText.textContent = `Total Risk Score: ${totalRisk.toFixed(2)}%`;
  totalText.textContent = `${totalRisk.toFixed(2)}% (100%)`;
  totalValue.textContent = `${totalRisk.toFixed(2)}%`;
  totalBar.style.width = `${totalRisk}%`;
}

// Update UI for a factor
function updateFactorUI(factor) {
  factor.textEl.textContent = `${factor.value}% (${factor.weight * 100}%)`;
  factor.valueEl.textContent = `${factor.value}%`;
  factor.barEl.style.width = `${factor.value}%`;
  
  if (factor.value >= 71) {
    factor.barEl.className = "progress-bar-fill high";
  } else if (factor.value >= 41) {
    factor.barEl.className = "progress-bar-fill medium";
  } else {
    factor.barEl.className = "progress-bar-fill low";
  }
}

// Event listeners
generateBtn.addEventListener('click', generateRandomData);

// Initialize UI
factors.forEach(updateFactorUI);
calculateTotalRisk();

// Socioeconomic Status Calculation
function calculateSocioeconomicRisk(stratum, income) {
  // Stratum score: Stratum 1 = 6 points, Stratum 6 = 1 point
  const stratumScore = 7 - stratum;
  
  // Income score: 1 = 1 point, 2 = 2 points, 3 = 3 points
  const incomeScore = income;
  
  // Total score
  const totalScore = stratumScore + incomeScore;
  
  // Calculate percentage: SE = ((Score - 2) / 7) * 100
  const percentage = ((totalScore - 2) / 7) * 100;
  
  return {
    stratumScore,
    incomeScore,
    totalScore,
    percentage,
    riskLevel: percentage >= 70 ? "High Risk" : percentage >= 40 ? "Medium Risk" : "Low Risk"
  };
}

function showToastError(message) {
    const toast = document.getElementById('toast-error');
    toast.textContent = message;
    toast.style.display = 'block';
    toast.style.opacity = '1';
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 500);
    }, 4000);
}

// Housing Environment Calculation
function calculateHousingRisk(zone, geographicRisks) {
  // Zone score: Urban = 1, Rural = 2
  const zoneScore = zone;
  
  // Geographic risks score: sum of all risk values
  const risksScore = geographicRisks.reduce((sum, risk) => sum + risk.value, 0);
  
  // Total score
  const totalScore = zoneScore + risksScore;
  
  // Maximum possible score (for percentage calculation)
  const maxScore = 2 + 27; // Rural (2) + all possible risks (27)
  
  // Calculate percentage
  const percentage = (totalScore / maxScore) * 100;
  
  return {
    zoneScore,
    risksScore,
    totalScore,
    percentage,
    riskLevel: percentage >= 70 ? "High Risk" : percentage >= 40 ? "Medium Risk" : "Low Risk"
  };
}