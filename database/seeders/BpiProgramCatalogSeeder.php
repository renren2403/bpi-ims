<?php

namespace Database\Seeders;

use App\Models\OperatingUnit;
use App\Models\PAP;
use App\Models\PerformanceIndicator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BpiProgramCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Map common OU abbreviations to database IDs
            $ouMap = [
                'PMO'     => OperatingUnit::where('code', 'PMO')->value('id'),
                'CPMD'    => OperatingUnit::where('code', 'CPMD')->value('id'),
                'PPSSD'   => OperatingUnit::where('code', 'PPD')->value('id'),
                'PPD'     => OperatingUnit::where('code', 'PPD')->value('id'),
                'NSQCS'   => OperatingUnit::where('code', 'NSQCS')->value('id'),
                'NPQSD'   => OperatingUnit::where('code', 'NPQSD')->value('id'),
                'CRPSD'   => OperatingUnit::where('code', 'CRD')->value('id'),
                'CRD'     => OperatingUnit::where('code', 'CRD')->value('id'),
                'ALGD'    => OperatingUnit::where('code', 'ALGD')->value('id'),
                'AED'     => OperatingUnit::where('code', 'AED')->value('id'),
                'ADMIN'   => OperatingUnit::where('code', 'ADMIN')->value('id'),
                'BNCRDC'  => OperatingUnit::where('code', 'BNCRDC')->value('id'),
                'LNCRDC'  => OperatingUnit::where('code', 'LNCRDC')->value('id'),
                'DNCRDC'  => OperatingUnit::where('code', 'DNCRDC')->value('id'),
                'GNCRDC'  => OperatingUnit::where('code', 'GNCRDC')->value('id'),
            ];

            $catalog = [
                // ==============================================================
                // CATEGORY I: GENERAL ADMINISTRATION AND SUPPORT SERVICES
                // ==============================================================
                [
                    'category' => 'I. General Administration and Support Services',
                    'pap_code' => '100000100001000',
                    'pap_name' => 'General Management and Supervision',
                    'items' => [
                        [
                            'sub_program' => 'General Management and Supervision',
                            'activity' => 'General Management and Supervision',
                            'ou' => 'ADMIN',
                            'name' => 'General Management and Supervision Operations Conducted',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'General Management and Supervision',
                            'activity' => 'Consultancy Service for the A&E Design of Six-Storey Admin Building',
                            'ou' => 'ADMIN',
                            'name' => 'Consultancy Service for the A&E Design of Six-Storey Admin Building Contracted',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'General Management and Supervision',
                            'activity' => 'Security, Janitorial, Fuel, ISO, PRIME',
                            'ou' => 'ADMIN',
                            'name' => 'Support Services Maintained (Security, Janitorial, Fuel, ISO, PRIME)',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Administration of Personnel Benefits',
                            'activity' => 'Administration of Personnel Benefits',
                            'ou' => 'ADMIN',
                            'name' => 'Administration of Personnel Benefits Processed',
                            'unit' => 'number',
                        ],
                    ],
                ],

                // ==============================================================
                // CATEGORY II: SUPPORT TO OPERATIONS (STO)
                // ==============================================================
                [
                    'category' => 'II. Support to Operations',
                    'pap_code' => '200000100001000',
                    'pap_name' => 'Information and Communication Technology (ICT) Management Support',
                    'items' => [
                        [
                            'sub_program' => 'ICT Infrastructure and Systems Support',
                            'activity' => 'Information and Communication Technology (ICT) Management Support',
                            'ou' => 'PMO',
                            'name' => 'ICT Hardware, Systems, and Network Infrastructure Maintained and Supported',
                            'unit' => 'number',
                        ],
                    ],
                ],
                [
                    'category' => 'II. Support to Operations',
                    'pap_code' => '200000100002000',
                    'pap_name' => 'Field Programs and Management Activities',
                    'items' => [
                        [
                            'sub_program' => 'Banner Program Coordination & Monitoring',
                            'activity' => 'Field Programs and Management Activities (Banner program activities such as: monitoring and evaluation, meetings, workshops, etc.)',
                            'ou' => 'PMO',
                            'name' => 'Field Programs, M&E, and Planning Consultations Conducted',
                            'unit' => 'number',
                        ],
                    ],
                ],
                [
                    'category' => 'II. Support to Operations',
                    'pap_code' => '200000100003000',
                    'pap_name' => 'Modernization and Strengthening of Production and Research Facilities',
                    'items' => [
                        // Sub-Program 1: Strengthening of CRSPD and Centers
                        [
                            'sub_program' => 'Strengthening of CRSPD and Centers',
                            'activity' => 'Production and distribution of quality planting materials (fruits, industrial crops, other crops) (CRSPD)',
                            'ou' => 'CRPSD',
                            'name' => 'Production and distribution of quality planting materials (fruits, industrial crops, other crops)',
                            'unit' => 'piece',
                        ],
                        [
                            'sub_program' => 'Strengthening of CRSPD and Centers',
                            'activity' => 'Maintenance of Production Facilities',
                            'ou' => 'CRPSD',
                            'name' => 'Maintenance of Production Facilities',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Strengthening of CRSPD and Centers',
                            'activity' => 'Conduct of meetings, trainings, workshop (CRPSD)',
                            'ou' => 'CRPSD',
                            'name' => 'Conduct of meetings, trainings, workshop (CRPSD)',
                            'unit' => 'number',
                        ],

                        // Sub-Program 2: Support to Sustainable Pest Management (CPMD)
                        [
                            'sub_program' => 'Support to Sustainable Pest Management (CPMD)',
                            'activity' => 'Pest Surveillance, monitoring and validation',
                            'ou' => 'CPMD',
                            'name' => 'Pest Surveillance, monitoring and validation',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Support to Sustainable Pest Management (CPMD)',
                            'activity' => 'Quality mother stocks biological control agents produced and distributed',
                            'ou' => 'CPMD',
                            'name' => 'Quality mother stocks biological control agents produced and distributed',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Support to Sustainable Pest Management (CPMD)',
                            'activity' => 'Plant pest diagnosed',
                            'ou' => 'CPMD',
                            'name' => 'Plant pest diagnosed',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Support to Sustainable Pest Management (CPMD)',
                            'activity' => 'Pest data management',
                            'ou' => 'CPMD',
                            'name' => 'Pest data management',
                            'unit' => 'number',
                        ],

                        // Sub-Program 3: Empowering Philippine Seed System (CRPSD, CPMD, NSQCS, AED and 5 Centers)
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Production of NSIC-Registered Legumes (Mungbean, Peanut, Soybean)',
                            'ou' => 'CRPSD',
                            'name' => 'Production of NSIC-Registered Legumes (Mungbean, Peanut, Soybean)',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Production of NSIC-Registered Tomato and Pole Sitao Lines of BPI-Los Baños NCRDPSC',
                            'ou' => 'LNCRDC',
                            'name' => 'Production of NSIC-Registered Tomato and Pole Sitao Lines of BPI-Los Baños NCRDPSC',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Production of NSIC-Registered Potato Lines of BPI-Baguio NCRDPSC',
                            'ou' => 'BNCRDC',
                            'name' => 'Production of NSIC-Registered Potato Lines of BPI-Baguio NCRDPSC',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => '10-LM Drainage Canal of BPI-GNCRDPSC repaired',
                            'ou' => 'GNCRDC',
                            'name' => '10-LM Drainage Canal of BPI-GNCRDPSC repaired',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => '4WD tractor procured',
                            'ou' => 'GNCRDC',
                            'name' => '4WD tractor procured',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Outscaling of the Citrus Healthy Plant Material Production Scheme Through Establishment of Nurseries and Certification of Plant Materials in Key Production Areas',
                            'ou' => 'CRPSD',
                            'name' => 'Citrus planting materials produced and distributed',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Promotion and Outscaling of Vegetable Grafting Technology for Improved Off-Season Production',
                            'ou' => 'CRPSD',
                            'name' => 'Grafted vegetable seedlings produced and distributed',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Conservation Through Utilization of Genetic Resources of Vegetable Plantation and Tropical Fruit Crops in the Philippines',
                            'ou' => 'CRPSD',
                            'name' => 'Facilities, Perimeter Fence, and Water Systems Rehabilitated in Support to PGR Conservation',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Strengthening of BPI-PGR Center Laboratory',
                            'ou' => 'CRPSD',
                            'name' => 'PGR Center Building and Molecular/Tissue Culture Laboratories Established',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Strengthening of a Molecular and Disease Indexing Laboratory at BPI-LBNCRDPSC in support to the Production of Disease Free Planting Materials',
                            'ou' => 'LNCRDC',
                            'name' => 'Molecular and Disease Indexing Laboratory Established and Equipped',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Evaluation on the Suitability of Onion Seed Production under Indoor Cultivation',
                            'ou' => 'CRPSD',
                            'name' => 'Containerized indoor cultivation system established',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Establishment and Maintenance of Foundation Scion Grove and Nursery',
                            'ou' => 'CRPSD',
                            'name' => 'Foundation scion grove and nursery established and maintained',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Development and Standardization of Scalable Fermentation and Formulation Technologies for Mass Production of Farmer-Ready Microbial Biocontrol Agents',
                            'ou' => 'CPMD',
                            'name' => 'Technical and scientific equipment procured for microbial biocontrol agents',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Empowering Philippine Seed System thorugh Genetic Resource Conservation, Quality Assurance, Pest and Diseases Biocontorl and Sustainable Production Technologies',
                            'activity' => 'Comprehensive Enhancement of the BPI-NSQCS Seed Quality Assurance System',
                            'ou' => 'NSQCS',
                            'name' => 'Disease indexing laboratory established and technical equipment procured',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Modernization and Strengthening of Production and Research Facilities',
                            'activity' => 'Support from Other Research and Development Activities (Realignment from ORDA to STO-Modernization)',
                            'ou' => 'CRPSD',
                            'name' => 'Research Facilities and Production Modernization Supported',
                            'unit' => 'number',
                        ],
                    ],
                ],

                // ==============================================================
                // CATEGORY III: OPERATIONS
                // ==============================================================
                [
                    'category' => 'III. Operations',
                    'pap_code' => '310100100001000',
                    'pap_name' => 'Technical and Support Services Program',
                    'items' => [
                        // PSS 1: Rice Program
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Rice Program',
                            'ou' => 'CRPSD',
                            'name' => 'Higher class of rice seeds (FS-RS) produced and distributed',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Rice Program',
                            'ou' => 'CRPSD',
                            'name' => 'Rice seed reserve procured',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Rice Program',
                            'ou' => 'CPMD',
                            'name' => 'Agri-chemical reserves and biocontrol agents distributed (Rice Program)',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Rice Program',
                            'ou' => 'CPMD',
                            'name' => 'Plant pest and disease diagnostics and early warning responses conducted',
                            'unit' => 'number',
                        ],

                        // PSS 2: Corn Program
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Corn Program',
                            'ou' => 'CRPSD',
                            'name' => 'Corn seeds (white flint, glutinous and yellow corn) produced and distributed',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Corn Program',
                            'ou' => 'CRPSD',
                            'name' => 'Production and distribution of quality cassava seed pieces',
                            'unit' => 'piece',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Corn Program',
                            'ou' => 'CRPSD',
                            'name' => 'Corn seed reserve procured',
                            'unit' => 'kilogram',
                        ],

                        // PSS 3: High Value Crops Program
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National High Value Crops Development Program',
                            'ou' => 'CRPSD',
                            'name' => 'Lowland, highland vegetables and legumes produced and distributed',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National High Value Crops Development Program',
                            'ou' => 'CRPSD',
                            'name' => 'Vegetable for bufferstocking produced and procured',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National High Value Crops Development Program',
                            'ou' => 'CRPSD',
                            'name' => 'Planting materials produced and procured for distribution',
                            'unit' => 'piece',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National High Value Crops Development Program',
                            'ou' => 'CPMD',
                            'name' => 'Support to Sustainable Pests and Disease Management of High Value Crops',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National High Value Crops Development Program',
                            'ou' => 'NSQCS',
                            'name' => 'Support to Ensure Quality Seeds and Planting Materials (seedling tagging & seed testing)',
                            'unit' => 'number',
                        ],

                        // PSS 4: Organic Agriculture Program
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the Promotion and Development of Organic Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Production and distribution of certified organic seeds',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the Promotion and Development of Organic Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Production and distribution of organic planting materials',
                            'unit' => 'piece',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the Promotion and Development of Organic Agriculture Program',
                            'ou' => 'CPMD',
                            'name' => 'Production and distribution of biological control agents (BCAs) (Organic Program)',
                            'unit' => 'piece',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the Promotion and Development of Organic Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Maintenance of organic seed areas and other production facilities',
                            'unit' => 'number',
                        ],

                        // PSS 5: Urban & Peri-Urban Agriculture Program
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Urban and Peri-Urban Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Production/procurement and distribution of lowland and highland vegetables and legumes',
                            'unit' => 'kilogram',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Urban and Peri-Urban Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Production/procurement of planting materials for distribution',
                            'unit' => 'piece',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Urban and Peri-Urban Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Soil mix / fertilizer distributed',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Urban and Peri-Urban Agriculture Program',
                            'ou' => 'CPMD',
                            'name' => 'Production and distribution of biological control agents (BCAs) (Urban Program)',
                            'unit' => 'piece',
                        ],
                        [
                            'sub_program' => 'Production Support Services (PSS)',
                            'activity' => 'PSS on the National Urban and Peri-Urban Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Support to production site (community garden and school garden)',
                            'unit' => 'number',
                        ],

                        // ESETS (Extension Support, Education, and Training Services)
                        [
                            'sub_program' => 'Extension Support, Education, and Training Services (ESETS)',
                            'activity' => 'ESETS on the National Rice Program',
                            'ou' => 'CRPSD',
                            'name' => 'Conduct of capacity building activities through trainings to GAP, Seed System Development, Crop Pest Management',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Extension Support, Education, and Training Services (ESETS)',
                            'activity' => 'ESETS on the National Rice Program',
                            'ou' => 'CRPSD',
                            'name' => 'Rice Information campaign activities',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Extension Support, Education, and Training Services (ESETS)',
                            'activity' => 'ESETS on the National Corn Program',
                            'ou' => 'CRPSD',
                            'name' => 'Conduct of training and training related activities (Seed inspector\'s training, corn growers training, public advocacy on crop pest management)',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Extension Support, Education, and Training Services (ESETS)',
                            'activity' => 'ESETS on the Promotion and Development of Organic Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Conduct of Training of Trainer\'s (TOT) on the establishment of local seed bank',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Extension Support, Education, and Training Services (ESETS)',
                            'activity' => 'ESETS on the National Urban and Peri-Urban Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Conduct of training and training related activities (TOT, POT, other trainings)',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Extension Support, Education, and Training Services (ESETS)',
                            'activity' => 'ESETS on the National Urban and Peri-Urban Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'IEC Activities conducted and Technology demonstration established',
                            'unit' => 'number',
                        ],

                        // Research and Development (R&D)
                        [
                            'sub_program' => 'Research and Development (R&D)',
                            'activity' => 'R&D on the National Corn Program',
                            'ou' => 'CRPSD',
                            'name' => 'Improvement of yellow corn cultivar, updating of corn online system, and adaptability trials',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Research and Development (R&D)',
                            'activity' => 'R&D on the Promotion and Development of Organic Agriculture Program',
                            'ou' => 'CRPSD',
                            'name' => 'Research on the Development of Organic Seed System',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Research and Development (R&D)',
                            'activity' => 'Other Research and Development Activities (Regular Fund)',
                            'ou' => 'CRPSD',
                            'name' => 'Conduct of production, extension and postharvest related researches (new, continuing, to be completed)',
                            'unit' => 'number',
                        ],

                        // Agricultural Machinery Program
                        [
                            'sub_program' => 'Agricultural Machinery, Equipment, Facilities and Infrastructure Program',
                            'activity' => 'National Urban and Peri-Urban Agriculture Program',
                            'ou' => 'AED',
                            'name' => 'Procurement and provision of agri-machineries, equipment, and production facilities',
                            'unit' => 'number',
                        ],
                    ],
                ],
                [
                    'category' => 'III. Operations',
                    'pap_code' => '310100100002000',
                    'pap_name' => 'Agriculture and Fishery Regulatory Support Program',
                    'items' => [
                        // Sub-Program 1: Quality Control and Regulatory Documents
                        // Component A: NSQCS (SeSeed)
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Securing Seed Quality (SeSeed) through Institutional Development for Adaptive, Stringent, and Assertive Approach to Seed Regulations (NSQCS)',
                            'ou' => 'NSQCS',
                            'name' => 'Seed growers/producers accredited',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Securing Seed Quality (SeSeed) through Institutional Development for Adaptive, Stringent, and Assertive Approach to Seed Regulations (NSQCS)',
                            'ou' => 'NSQCS',
                            'name' => 'Seed certification (seed bags tagged)',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Securing Seed Quality (SeSeed) through Institutional Development for Adaptive, Stringent, and Assertive Approach to Seed Regulations (NSQCS)',
                            'ou' => 'NSQCS',
                            'name' => 'Plant nursery accredited',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Securing Seed Quality (SeSeed) through Institutional Development for Adaptive, Stringent, and Assertive Approach to Seed Regulations (NSQCS)',
                            'ou' => 'NSQCS',
                            'name' => 'Plant material certification (seedlings tagged)',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Securing Seed Quality (SeSeed) through Institutional Development for Adaptive, Stringent, and Assertive Approach to Seed Regulations (NSQCS)',
                            'ou' => 'NSQCS',
                            'name' => 'Plant tissue culture facility accredited',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Securing Seed Quality (SeSeed) through Institutional Development for Adaptive, Stringent, and Assertive Approach to Seed Regulations (NSQCS)',
                            'ou' => 'NSQCS',
                            'name' => 'Seed Quality Laboratory Building in Region XI (NSQCS) established',
                            'unit' => 'number',
                        ],

                        // Component B: PPSSD (Pagkalinga ng Pagkain)
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Pagkalinga ng Pagkain (Food Safety Analysis, PhilGAP Certification and Licensing of Food Business Operators) (PPSSD)',
                            'ou' => 'PPSSD',
                            'name' => 'Food safety analysis (samples analyzed)',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Pagkalinga ng Pagkain (Food Safety Analysis, PhilGAP Certification and Licensing of Food Business Operators) (PPSSD)',
                            'ou' => 'PPSSD',
                            'name' => 'GAP farm certified',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Pagkalinga ng Pagkain (Food Safety Analysis, PhilGAP Certification and Licensing of Food Business Operators) (PPSSD)',
                            'ou' => 'PPSSD',
                            'name' => 'Food Business Operators licensed',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Upgrading and Capacitation of BPI Food Safety Laboratories under the Philippine Sustainable Agriculture Transformation (PSAT) Program (PPSSD)',
                            'ou' => 'PPSSD',
                            'name' => 'Capacitation and modernization of food safety laboratories and sampling vehicles',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Capacitation and Modernization of Plant Quarantine Laboratories under the Philippine Sustainable Agriculture Transformation (PAST) Program',
                            'ou' => 'NPQSD',
                            'name' => 'Capacitation and Modernization of Plant Quarantine Laboratories',
                            'unit' => 'number',
                        ],

                        // Component C: Biotech & GMO Oversight
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Supporting to Agricultural Biotechnology Oversight for PBI Products and GMOs through Regulatory Capacity Building, Stakeholder Engagement and Laboratory Activities (Biotech)',
                            'ou' => 'CRPSD',
                            'name' => 'Biosafety permits issued',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Supporting to Agricultural Biotechnology Oversight for PBI Products and GMOs through Regulatory Capacity Building, Stakeholder Engagement and Laboratory Activities (Biotech)',
                            'ou' => 'CRPSD',
                            'name' => 'Monitoring of Industries\' Compliance with Biosafety conditions',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Supporting to Agricultural Biotechnology Oversight for PBI Products and GMOs through Regulatory Capacity Building, Stakeholder Engagement and Laboratory Activities (Biotech)',
                            'ou' => 'CRPSD',
                            'name' => 'Issuance of certificate for refuge sampling and testing and Non-Coverage',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Supporting to Agricultural Biotechnology Oversight for PBI Products and GMOs through Regulatory Capacity Building, Stakeholder Engagement and Laboratory Activities (Biotech)',
                            'ou' => 'CRPSD',
                            'name' => 'Detection of GM Events using Protein based and Molecular Based Methods',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quality Control and Inspection and Issuance of Regulatory Documents',
                            'activity' => 'Support to the National Seed Industry Council for the Implementation of Crop Variety Registration (NSIC)',
                            'ou' => 'NSQCS',
                            'name' => 'Plant variety registered',
                            'unit' => 'number',
                        ],

                        // Sub-Program 2: Quarantine Service (NPQSD)
                        [
                            'sub_program' => 'Quarantine Service',
                            'activity' => 'Strengthening of Border Control and Expansion of International and Local Market Access (NPQSD)',
                            'ou' => 'NPQSD',
                            'name' => 'Import clearance issued',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quarantine Service',
                            'activity' => 'Strengthening of Border Control and Expansion of International and Local Market Access (NPQSD)',
                            'ou' => 'NPQSD',
                            'name' => 'Phytosanitary certificates issued',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quarantine Service',
                            'activity' => 'Strengthening of Border Control and Expansion of International and Local Market Access (NPQSD)',
                            'ou' => 'NPQSD',
                            'name' => 'Clearance for domestic transport issued',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quarantine Service',
                            'activity' => 'Strengthening of Border Control and Expansion of International and Local Market Access (NPQSD)',
                            'ou' => 'NPQSD',
                            'name' => 'Agriculture facilities and products monitored and/or inspected',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quarantine Service',
                            'activity' => 'Strengthening of Border Control and Expansion of International and Local Market Access (NPQSD)',
                            'ou' => 'NPQSD',
                            'name' => 'Application for quarantine permits and SPS clearances processed within one (1) day',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quarantine Service',
                            'activity' => 'Strengthening of Border Control and Expansion of International and Local Market Access (NPQSD)',
                            'ou' => 'NPQSD',
                            'name' => 'First and second border inspections conducted',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quarantine Service',
                            'activity' => 'Strengthening of Border Control and Expansion of International and Local Market Access (NPQSD)',
                            'ou' => 'NPQSD',
                            'name' => 'Guidelines published and laboratory pest/disease diagnostics conducted',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Quarantine Service',
                            'activity' => 'Construction of NPQS Office Buildings (NPQSD)',
                            'ou' => 'NPQSD',
                            'name' => 'Construction of NPQS Office Buildings',
                            'unit' => 'number',
                        ],
                    ],
                ],
                [
                    'category' => 'Locally-Funded Projects',
                    'pap_code' => 'LFP-PROJECTS',
                    'pap_name' => 'Locally-Funded Project',
                    'items' => [
                        [
                            'sub_program' => 'Special Agricultural Development Projects',
                            'activity' => 'Coffee Industry Development Project',
                            'ou' => 'CRPSD',
                            'name' => 'Coffee Industry Development Project Targets Accomplished',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Special Agricultural Development Projects',
                            'activity' => 'High Value Export Crops Development Project',
                            'ou' => 'CRPSD',
                            'name' => 'High Value Export Crops Development Project Targets Accomplished',
                            'unit' => 'number',
                        ],
                    ],
                ],
                [
                    'category' => 'Automatic Appropriations',
                    'pap_code' => 'AUTO-APPROP',
                    'pap_name' => 'Automatic Appropriations',
                    'items' => [
                        [
                            'sub_program' => 'Statutory Obligations',
                            'activity' => 'Retirement and Life Insurance Premium (RLIP)',
                            'ou' => 'ADMIN',
                            'name' => 'Retirement and Life Insurance Premium Remitted',
                            'unit' => 'number',
                        ],
                        [
                            'sub_program' => 'Statutory Obligations',
                            'activity' => 'Special Account in the General Fund (Seed Fund)',
                            'ou' => 'NSQCS',
                            'name' => 'Special Account in the General Fund (Seed Fund) Administered',
                            'unit' => 'number',
                        ],
                    ],
                ],
            ];

            $indCount = 0;
            foreach ($catalog as $group) {
                // Upsert PAP
                $pap = PAP::updateOrCreate(
                    ['code' => $group['pap_code']],
                    [
                        'name' => $group['pap_name'],
                        'category' => $group['category'],
                        'is_active' => true,
                    ]
                );

                foreach ($group['items'] as $item) {
                    $indCount++;
                    $ouId = $ouMap[$item['ou']] ?? null;

                    PerformanceIndicator::updateOrCreate(
                        [
                            'pap_id' => $pap->id,
                            'sub_program' => $item['sub_program'],
                            'activity' => $item['activity'],
                            'name' => $item['name'],
                        ],
                        [
                            'operating_unit_id' => $ouId,
                            'unit_of_measure' => $item['unit'] ?? 'number',
                            'code' => $pap->code . '-IND-' . str_pad($indCount, 3, '0', STR_PAD_LEFT),
                            'type' => 'output',
                            'is_active' => true,
                        ]
                    );
                }
            }
        });
    }
}
