import axios from 'axios';

// Fetch all buyer lists
export async function fetchBuyerLists() {
  const res = await axios.get('/api/buyer-lists');
  return res.data;
}

// Save a buyer list
export async function saveBuyerList(name: string, buyers: any[]) {
  await axios.post('/api/buyer-lists', {
    name,
    buyers, // send the full array of buyer objects!
  });
}

// Delete a buyer list
export async function deleteBuyerList(name: string) {
  await axios.delete(`/api/buyer-lists/${encodeURIComponent(name)}`);
}
