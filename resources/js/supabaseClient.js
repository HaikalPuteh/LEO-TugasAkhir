import { createClient } from '@supabase/supabase-js';

const supabaseUrl = 'https://mzwqjldxxrbmguuxypcz.supabase.co'; // Ganti dengan URL proyek Supabase Anda
const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im16d3FqbGR4eHJibWd1dXh5cGN6Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDY3MTIyMzMsImV4cCI6MjA2MjI4ODIzM30.AJdm9U4nL5xlN5ciX3E9K7M-VUiwqb9fe1XQG7llKj4'; // Ganti dengan anon public key Supabase Anda

export const supabase = createClient(supabaseUrl, supabaseAnonKey);