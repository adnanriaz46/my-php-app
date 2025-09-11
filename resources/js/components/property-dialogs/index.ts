import { ZillowPListingMain } from "@/lib/zilowAndlocationUtil";
import { DBApiAverageCompsProperty, DBApiCalValData, DBApiPropertyFull, DBApiPropertyList } from "@/types/DBApi";

export { default as InstantOfferDialog } from './InstantOfferDialog.vue'
export { default as RequestShowingDialog } from './RequestShowingDialog.vue'
export { default as AskAQuestionDialog } from './AskAQuestionDialog.vue'


export const getZillowAIMessage = (zillowData: ZillowPListingMain, calculatedData?: DBApiCalValData, averageCompsData?: DBApiAverageCompsProperty): string => {

  // Zillow Property Data:
  // \`\`\`json
  // ${JSON.stringify(zillowData, null, 2)}
  // \`\`\`

  return `
You are an AI assistant helping with real estate property analysis.
Below is all the data you need to analyze an unlisted property or answer questions about the property.
Use all the provided information to give detailed, accurate, and helpful responses.

Information Type: Unlisted Property Details (Unlisted on Revamp365)


Key Property Information:
- TAX ID Number: ${zillowData?.resoFacts?.parcelNumber || 'Not available'}
- Street Address: ${zillowData?.streetAddress || 'Not available'}
- City: ${zillowData?.city || 'Not available'}
- State: ${zillowData?.state || 'Not available'}
- Zip Code: ${zillowData?.zipcode || 'Not available'}
- County: ${zillowData?.county || 'Not available'}
- Bedrooms: ${zillowData?.bedrooms || 'Not available'}
- Bathrooms: ${zillowData?.bathrooms || 'Not available'}
- Property Type: ${zillowData?.propertyTypeDimension || 'Not available'}
- Total Sqft: ${zillowData?.livingArea ? `${zillowData.livingArea.toLocaleString()} sqft` : 'Not available'}
- Lot Size: ${zillowData?.resoFacts?.lotSize || 'Not available'}
- Year Built: ${zillowData?.yearBuilt || 'Not available'}
- Latitude: ${zillowData?.latitude || 'Not available'}
- Longitude: ${zillowData?.longitude || 'Not available'}
- Zestimate: ${zillowData?.zestimate ? `$${zillowData.zestimate.toLocaleString()}` : 'Not available'}
- Rent Zestimate: ${zillowData?.rentZestimate ? `$${zillowData.rentZestimate.toLocaleString()}/month` : 'Not available'}
- Zestimate High Percent: ${zillowData?.zestimateHighPercent ? `${(parseFloat(zillowData.zestimateHighPercent) * 100).toFixed(1)}%` : 'Not available'}
- Stories: ${zillowData?.resoFacts?.storiesDecimal || 'Not available'}
- Parking Features: ${zillowData?.resoFacts?.parkingFeatures?.join(', ') || 'Not available'}
- Exterior Features: ${zillowData?.resoFacts?.exteriorFeatures?.join(', ') || 'Not available'}
- Room Types: ${zillowData?.resoFacts?.roomTypes?.join(', ') || 'Not available'}
- Other Facts: ${zillowData?.resoFacts?.otherFacts?.map(fact => `${fact.name}: ${fact.value}`).join(', ') || 'Not available'}


Tax Information:
- Last Tax Assessed Value: ${zillowData?.taxHistory?.[0]?.value}
- Last Tax Annual Amount: ${zillowData?.taxHistory?.[0]?.taxPaid}

Property Features:
- Heating: ${zillowData?.resoFacts?.hasHeating ? 'Yes' : 'No'}
- Cooling: ${zillowData?.resoFacts?.hasCooling ? 'Yes' : 'No'}

Price History: ${zillowData?.priceHistory?.map(v => `$${v.price} on ${v.date}`).join(', ') || 'No price history available'}

Description: ${zillowData?.description || 'No description available'}

Average Listing Data (Market Analysis):
\`\`\`json
${JSON.stringify(averageCompsData, null, 2)}
\`\`\`

Calculated Data (Additional Analysis):
\`\`\`json
${JSON.stringify(calculatedData || {}, null, 2)}
\`\`\`


Instructions:
- Use all the above data to answer questions or analyze this unlisted property.
- If the user asks for a summary, provide a concise overview of the property and market position.
- If the user asks for analysis, highlight key points, market trends, or investment potential.
- If the user asks for recommendations, use the data to support your suggestions.
- Consider this is an unlisted property (not on MLS) when providing analysis.

Example User Prompts:
- "What is the estimated value of this unlisted property?"
- "How does this property compare to the market?"
- "What are the investment opportunities for this property?"
- "What are the strengths and weaknesses of this property?"
- "Should I consider making an offer on this property?"

End of Data.
    `;
}


export const getCompsReportMessage = (
  zillowData: ZillowPListingMain,
  averageCompsData: DBApiAverageCompsProperty | null,
  propertyListData: DBApiPropertyList[],
  estArv?: number | null
): string => {
  return `
  You are an AI assistant helping with real estate property analysis.
  Below is all the data you need to generate a comps report or answer questions about the property.
  Use all the provided information to give detailed, accurate, and helpful responses.
  
  Information Type: Comps Report

  Subject Property Information:
  - TAX ID Number: ${zillowData?.resoFacts?.parcelNumber || 'Not available'}
  - Street Address: ${zillowData?.streetAddress || 'Not available'}
  - City: ${zillowData?.city || 'Not available'}
  - State: ${zillowData?.state || 'Not available'}
  - Zip Code: ${zillowData?.zipcode || 'Not available'}
  - County: ${zillowData?.county || 'Not available'}
  - Bedrooms: ${zillowData?.bedrooms || 'Not available'}
  - Bathrooms: ${zillowData?.bathrooms || 'Not available'}
  - Property Type: ${zillowData?.propertyTypeDimension || 'Not available'}
  - Total Sqft: ${zillowData?.livingArea ? `${zillowData.livingArea.toLocaleString()} sqft` : 'Not available'}
  - Lot Size: ${zillowData?.resoFacts?.lotSize || 'Not available'}
  - Year Built: ${zillowData?.yearBuilt || 'Not available'}
  - Latitude: ${zillowData?.latitude || 'Not available'}
  - Longitude: ${zillowData?.longitude || 'Not available'}
  - Zestimate: ${zillowData?.zestimate ? `$${zillowData.zestimate.toLocaleString()}` : 'Not available'}
  - Rent Zestimate: ${zillowData?.rentZestimate ? `$${zillowData.rentZestimate.toLocaleString()}/month` : 'Not available'}
  - Zestimate High Percent: ${zillowData?.zestimateHighPercent ? `${(parseFloat(zillowData.zestimateHighPercent) * 100).toFixed(1)}%` : 'Not available'}
  - Stories: ${zillowData?.resoFacts?.storiesDecimal || 'Not available'}
  - Parking Features: ${zillowData?.resoFacts?.parkingFeatures?.join(', ') || 'Not available'}
  - Exterior Features: ${zillowData?.resoFacts?.exteriorFeatures?.join(', ') || 'Not available'}
  - Room Types: ${zillowData?.resoFacts?.roomTypes?.join(', ') || 'Not available'}
  - Other Facts: ${zillowData?.resoFacts?.otherFacts?.map(fact => `${fact.name}: ${fact.value}`).join(', ') || 'Not available'}
  
  
  Tax Information:
  - Last Tax Assessed Value: ${zillowData?.taxHistory?.[0]?.value}
  - Last Tax Annual Amount: ${zillowData?.taxHistory?.[0]?.taxPaid}
  
  
  Property Features:
  - Heating: ${zillowData?.resoFacts?.hasHeating ? 'Yes' : 'No'}
  - Cooling: ${zillowData?.resoFacts?.hasCooling ? 'Yes' : 'No'}
  
  Price History: ${zillowData?.priceHistory?.map(v => `$${v.price} on ${v.date}`).join(', ') || 'No price history available'}
  
  Description: ${zillowData?.description || 'No description available'}

  Comps Report Data (Averages):
  \`\`\`json
    ${JSON.stringify(averageCompsData, null, 2)}
  \`\`\`
  
  Comparable Properties List Data:
  \`\`\`json
  ${JSON.stringify(propertyListData, null, 2)}
  \`\`\`
  
  Comps Calculated Data:
  Estimated ARV: ${estArv}


  Instructions:
  - Use all the above data to answer questions or generate a comps report.
  - If the user asks for a summary, provide a concise overview of the property and its comparables.
  - If the user asks for analysis, highlight key points, trends, or outliers in the data.
  - If the user asks for recommendations, use the data to support your suggestions.
  
  Example User Prompts:
  - "What is the estimated value of this property based on the comps?"
  - "How does this property compare to others in the area?"
  - "Are there any notable trends in the comparable sales data?"
  - "What are the strengths and weaknesses of this property based on the data?"
  
  End of Data.
    `;
};


export const getAiSummaryPrompt = (zillowData: ZillowPListingMain,
  propertyData: DBApiPropertyFull,
  calculatedData: DBApiCalValData | null) => {
  return `
 You are summary generator with subject property and comps property data.
  Below is all the data you need to generate a summary of the property.
Generate a **one sentence of 12 words or fewer** highlighting the most impactful difference or similarity **NOT already visible in our thumbnail** (price, beds, baths, square footage, lot size, year built, DOM, distance, etc.).
Focus on factors that materially influence desirability or value, such as:
• Street type/traffic (main road, cul-de-sac, corner lot)
• Adjacent influences (park, railroad, highway, commercial zone, waterfront)
• Exact location (same street, different township, school district)
• Lot orientation/views (backs to woods, private yard)
• Unique features or condition cues (renovated, pool, dated, deferred maintenance)
If no meaningful distinction exists, output: **“No notable external differences.”**
Do **not** exceed 12 words, do **not** repeat visible stats, and output **only** the sentence.


  Information Type: Summary of this property compared to the subject property

  This Property Data(Comps):
  \`\`\`json
  ${JSON.stringify(propertyData, null, 2)}
  \`\`\`
  
  Subject Property Data:
  \`\`\`json
  ${JSON.stringify(zillowData, null, 2)}
  \`\`\`

  Subject Property Calculated Data:
  \`\`\`json
  ${JSON.stringify(calculatedData, null, 2)}
  \`\`\`

  End of Data.


    `
}