<script setup lang="ts">
import { DBApiAverageCompsProperty, DBApiCalValData, DBApiPropertyList } from '@/types/DBApi';
import { useNumber } from '@/composables/useFormat';
import { Ref } from 'vue';
import { pageVars } from '@/pages/guest-page/vars/vars';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

interface Props {
    calculatedValues?: DBApiCalValData | null;
    compsAverageData: Ref<{ data?: DBApiAverageCompsProperty } | undefined> | { data?: DBApiAverageCompsProperty } | undefined;
    filteredProperties: Ref<DBApiPropertyList[] | undefined> | DBApiPropertyList[] | undefined;
    zillowData?: any; // Add zillow data for property address
    arv?: number;
}

const props = defineProps<Props>();

const { formatNumber, formatPrice } = useNumber();

const googleMapApiKey = import.meta.env.VITE_GOOGLE_MAP_API;

// Helper functions to safely access data
const getCompsData = () => {
    if (props.compsAverageData && 'value' in props.compsAverageData) {
        return props.compsAverageData.value?.data;
    }
    return props.compsAverageData?.data;
};

const getFilteredProperties = () => {
    if (props.filteredProperties && 'value' in props.filteredProperties) {
        return props.filteredProperties.value;
    }
    return props.filteredProperties;
};

// Generate static map URL
const getStaticMapUrl = () => {
    const subjectLat = props.zillowData?.latitude;
    const subjectLng = props.zillowData?.longitude;

    if (!subjectLat || !subjectLng) {
        // Fallback to a simple map placeholder
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk1hcCBVbmF2YWlsYWJsZTwvdGV4dD48L3N2Zz4=';
    }

    // Try Google Static Maps API first
    if (googleMapApiKey) {
        try {
            // Create markers for subject and comparable properties
            let markers = `markers=color:red%7Clabel:S%7C${subjectLat},${subjectLng}`;

            const comps = getFilteredProperties();
            if (comps) {
                comps.forEach((comp, index) => {
                    if (comp.latitude && comp.longitude) {
                        markers += `&markers=color:blue%7Clabel:${index + 1}%7C${comp.latitude},${comp.longitude}`;
                    }
                });
            }

            return `https://maps.googleapis.com/maps/api/staticmap?size=600x400&maptype=roadmap&${markers}&key=${googleMapApiKey}`;
        } catch (error) {
            console.warn('Google Static Maps API failed, using fallback:', error);
        }
    }

    // Fallback: Create a simple SVG map placeholder
    const comps = getFilteredProperties();
    const totalProperties = comps ? comps.length + 1 : 1; // +1 for subject property

    return `data:image/svg+xml;base64,${btoa(`
        <svg width="600" height="400" xmlns="http://www.w3.org/2000/svg">
            <rect width="100%" height="100%" fill="#f0f0f0"/>
            <text x="50%" y="45%" font-family="Arial" font-size="16" fill="#333" text-anchor="middle">Property Map</text>
            <text x="50%" y="60%" font-family="Arial" font-size="12" fill="#666" text-anchor="middle">Subject Property + ${totalProperties - 1} Comparable Properties</text>
            <text x="50%" y="75%" font-family="Arial" font-size="10" fill="#999" text-anchor="middle">Location: ${subjectLat}, ${subjectLng}</text>
        </svg>
    `)}`;
};

// Get property address for filename and header
const getPropertyAddress = () => {
    if (props.zillowData?.address?.streetAddress) {
        return `${props.zillowData.address.streetAddress}, ${props.zillowData.address.city}, ${props.zillowData.address.state} ${props.zillowData.address.zipcode}`;
    }
    return 'Subject Property';
};

// Generate filename
const getFilename = () => {
    const address = getPropertyAddress().replace(/[^a-zA-Z0-9]/g, '-');
    const date = new Date().toISOString().split('T')[0];
    return `comps-report-${address}-${date}.pdf`;
};

const downloadReport = async () => {
    console.log('Downloading report');

    // Create an iframe to completely isolate from global CSS
    const iframe = document.createElement('iframe');
    iframe.style.position = 'absolute';
    iframe.style.left = '-9999px';
    iframe.style.top = '0';
    iframe.style.width = '800px';
    iframe.style.height = '1200px';
    iframe.style.border = 'none';
    document.body.appendChild(iframe);

    // Wait for iframe to load
    await new Promise(resolve => {
        iframe.onload = resolve;
        iframe.src = 'about:blank';
    });

    const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document;
    if (!iframeDoc) {
        throw new Error('Could not access iframe document');
    }

    // Set up iframe document
    iframeDoc.open();
    iframeDoc.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #000000;
                    background: #ffffff;
                }
            </style>
        </head>
        <body>
            <div style="width: 800px; padding: 20px; background: white;">
                <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px;">
                    <img src="${pageVars.logoBlackLocal}" alt="Revamp365" style="max-width: 150px; max-height: 50px; height: auto; margin-bottom: 5px;">
                    <div style="font-size: 28px; font-weight: bold; margin-bottom: 10px; color: #333;">Comps Report</div>
                    <div style="font-size: 16px; color: #666; margin-bottom: 10px;">Estimated After Repair Value Analysis</div>
                    <div style="font-size: 14px; color: #333; font-weight: 500; margin-bottom: 10px;">${getPropertyAddress()}</div>
                    <div style="font-size: 12px; color: #666;">Generated on ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</div>
                </div>

                <div style="margin-bottom: 15px;">
                    <div style="font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #333;">Key Statistics</div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                        <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #dcfce7; border-color: #22c55e;">
                            <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Estimated ARV</div>
                            <div style="font-size: 24px; font-weight: bold; color: #333;">$${formatNumber(props.arv ?? 0)}</div>
                        </div>
                        <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #f9f9f9;">
                            <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Avg Square Feet</div>
                            <div style="font-size: 24px; font-weight: bold; color: #333;">${formatNumber(getCompsData()?.avg_sqft ?? 0)} sqft</div>
                        </div>
                        <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #f9f9f9;">
                            <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Sale Price Per Square Foot</div>
                            <div style="font-size: 24px; font-weight: bold; color: #333;">$${formatNumber(getCompsData()?.avg_closed_ppsf ?? 0)}</div>
                        </div>
                        <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #f9f9f9;">
                            <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Average Sale Price</div>
                            <div style="font-size: 24px; font-weight: bold; color: #333;">${formatPrice(getCompsData()?.avg_sales ?? 0)}</div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <div style="font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #333;">Average Characteristics</div>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">Characteristic</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">Value</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">Characteristic</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">Value</th>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">Average Bedrooms</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">${getCompsData()?.avg_beds ?? 0}</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">Average Distance</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">${formatNumber(getCompsData()?.avg_dist ?? 0, 2)} miles</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">Average Bathrooms</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">${getCompsData()?.avg_baths ?? 0}</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">Average DOM</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">${formatNumber(getCompsData()?.avg_dom ?? 0)}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">Average Lot Size</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;">${formatNumber(getCompsData()?.avg_lot_size ?? 0)} sqft</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;"></td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: left; color: #333;"></td>
                        </tr>
                    </table>
                </div>
                <div style="margin-bottom: 15px;">
                    <div style="font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #333;">Property Locations</div>
                    <div style="width: 100%; height: 300px; border: 1px solid #ddd; margin-bottom: 5px; display: flex; align-items: center; justify-content: center; background: #f0f0f0;">
                        <img src="${getStaticMapUrl()}" alt="Property Map" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="font-size: 12px; color: #666; text-align: center;">
                        Map showing ${getFilteredProperties()?.length ?? 0} comparable properties and subject property location
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <div style="font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #333;">Individual Comparable Properties</div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">#</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">ADDRESS</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">SALE PRICE</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">SALE DATE</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">SQFT</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">PRICE/SQ FT</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">BED</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">BATH</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5; font-weight: bold; color: #333;">DISTANCE</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${getFilteredProperties()?.map((comp, index) => `
                                <tr>
                                 <td style="border: 1px solid #ddd; padding: 8px; text-align: left; color: #333;">${index + 1}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left; color: #333;">${comp.address}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left; color: #333;">$${formatNumber(comp.close_price)}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left; color: #333;">${comp.close_date}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left; color: #333;">${formatNumber(comp.total_finished_sqft)}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left; color: #333;">$${comp.price_per_sqft_closed}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left; color: #333;">${comp.bedrooms_count}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left; color: #333;">${comp.bathrooms_total_count}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left; color: #333;">${comp.mile_range_from_subject} mi</td>
                                </tr>
                            `).join('') || ''}
                        </tbody>
                    </table>
                </div>
            </div>
        </body>
        </html>
    `);
    iframeDoc.close();

    // Wait a bit for the iframe to render
    await new Promise(resolve => setTimeout(resolve, 100));

    try {
        // Get the body element from iframe
        const iframeBody = iframeDoc.body;

        // Convert the iframe body to canvas
        const canvas = await html2canvas(iframeBody, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            width: 800,
            height: iframeBody.scrollHeight
        });

        // Create PDF
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');

        const imgWidth = 210; // A4 width in mm
        const pageHeight = 295; // A4 height in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;

        let position = 0;

        // Add first page
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Add additional pages if needed
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        // Save the PDF
        pdf.save(getFilename());

    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF. Please try again.');
    } finally {
        // Clean up
        document.body.removeChild(iframe);
    }
};

defineExpose({
    downloadReport
});
</script>

<template>
    <div>
        <!-- This component doesn't render anything visible -->
    </div>
</template>